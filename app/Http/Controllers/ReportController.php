<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Construye la consulta base con los filtros aplicados
     */
    private function buildInventoryQuery(Request $request)
    {
        $query = Inventory::where('estado', 'Disponible');

        // Restricción de Fármacos Controlados según rol/permiso
        $user = auth()->user();
        $puedeVerControlados = $user && ($user->hasRole('Administrador') || $user->can('ver.despacho_controlado'));

        if (! $puedeVerControlados) {
            $query->where('categoria', '!=', 'Fármacos Controlados');
        }

        // 1. Filtro de Búsqueda (Texto general)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('producto', 'like', "%{$search}%")
                    ->orWhere('lote', 'like', "%{$search}%")
                    ->orWhere('categoria', 'like', "%{$search}%")
                    ->orWhere('rut_proveedor', 'like', "%{$search}%");
            });
        }

        // 2. Filtro de Categoría
        if ($request->filled('categoria')) {
            if ($request->input('categoria') !== 'Fármacos Controlados' || $puedeVerControlados) {
                $query->where('categoria', $request->input('categoria'));
            }
        }

        // 3. Filtro de Pasillo
        if ($request->filled('pasillo')) {
            $query->where('pasillo', $request->input('pasillo'));
        }

        // 3b. Filtro de Programa
        if ($request->filled('programa')) {
            $query->where('programa', $request->input('programa'));
        }

        // 4. Filtro de Estante
        if ($request->filled('estante')) {
            $query->where('estante', $request->input('estante'));
        }

        // 5. Ordenamiento
        $orderBy = $request->input('order_by', 'recent');
        switch ($orderBy) {
            case 'nombre_asc':
                $query->orderBy('producto', 'asc');
                break;
            case 'stock_desc':
                $query->orderBy('cantidad', 'desc');
                break;
            case 'stock_asc':
                $query->orderBy('cantidad', 'asc');
                break;
            case 'vencimiento':
                $query->orderByRaw('CASE WHEN fecha_vencimiento IS NULL THEN 1 ELSE 0 END ASC, fecha_vencimiento ASC');
                break;
            case 'recent':
            default:
                $query->latest();
                break;
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->buildInventoryQuery($request);
        $inventories = $query->paginate(10)->withQueryString();

        $valorTotal = (clone $this->buildInventoryQuery($request))
            ->selectRaw('SUM(cantidad * valor_neto_unitario) as total')
            ->value('total') ?? 0;

        $programasUnicos = Inventory::where('estado', 'Disponible')
            ->whereNotNull('programa')
            ->where('programa', '!=', '')
            ->distinct()
            ->orderBy('programa')
            ->pluck('programa');

        $pasillosUnicos = Inventory::where('estado', 'Disponible')
            ->whereNotNull('pasillo')
            ->where('pasillo', '!=', '')
            ->distinct()
            ->orderBy('pasillo')
            ->pluck('pasillo');

        $estantesUnicos = Inventory::where('estado', 'Disponible')
            ->whereNotNull('estante')
            ->where('estante', '!=', '')
            ->distinct()
            ->orderBy('estante')
            ->pluck('estante');

        return view('reports.index', compact('inventories', 'valorTotal', 'programasUnicos', 'pasillosUnicos', 'estantesUnicos'));
    }

    public function exportExcel(Request $request)
    {
        $query = $this->buildInventoryQuery($request);
        return Excel::download(new InventoryExport($query), 'reporte_inventario_'.date('Ymd_His').'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = $this->buildInventoryQuery($request);
        $inventories = $query->get(); // Extrae todos los resultados sin paginar

        $pdf = Pdf::loadView('reports.pdf', compact('inventories'));
        $pdf->setPaper('A4', 'landscape'); // Formato horizontal para que quepan las columnas

        return $pdf->download('reporte_inventario_'.date('Ymd_His').'.pdf');
    }
}
