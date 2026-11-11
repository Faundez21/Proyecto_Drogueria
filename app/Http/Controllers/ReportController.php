<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Services\InventoryService;
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
            $query->whereNotIn('categoria', ['Fármacos Controlados', 'Controlado', 'Estupefacientes']);
        }

        // 1. Filtro de Búsqueda (Texto general)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('producto', 'like', "%{$search}%")
                    ->orWhere('lote', 'like', "%{$search}%")
                    ->orWhere('categoria', 'like', "%{$search}%")
                    ->orWhere('programa', 'like', "%{$search}%")
                    ->orWhere('rut_proveedor', 'like', "%{$search}%");
            });
        }

        // 2. Filtro de Categoría: se compara tal cual viene en los datos.
        if ($request->filled('categoria')) {
            $cat = trim((string) $request->input('categoria'));
            $esCatControlada = InventoryService::clasificarGrupoProducto('', $cat) === 'Estupefacientes';

            if (! $esCatControlada || $puedeVerControlados) {
                $query->where('categoria', $cat);
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

        $user = auth()->user();
        $puedeVerControlados = $user && ($user->hasRole('Administrador') || $user->can('ver.despacho_controlado'));

        $categoriasBD = Inventory::where('estado', 'Disponible')
            ->whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->when(! $puedeVerControlados, fn ($q) => $q->whereNotIn('categoria', ['Fármacos Controlados', 'Controlado', 'Estupefacientes']))
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        $categoriasUnicas = $categoriasBD->unique()->values();

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

        return view('reports.index', compact('inventories', 'valorTotal', 'categoriasUnicas', 'programasUnicos', 'pasillosUnicos', 'estantesUnicos'));
    }

    public function exportExcel(Request $request)
    {
        $query = $this->buildInventoryQuery($request);
        return Excel::download(new InventoryExport($query), 'reporte_inventario_'.date('Ymd_His').'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        // Un reporte con todo el inventario (más de mil lotes) necesita más memoria y tiempo
        // que el límite normal de PHP (128 MB). Se amplía solo para esta descarga.
        @ini_set('memory_limit', '1024M');
        @set_time_limit(300);

        $query = $this->buildInventoryQuery($request);

        // Salvaguarda DomPDF: la plantilla imprime la tabla en bloques de 100 filas, lo que permite
        // generar el reporte completo con cerca de 350 MB por cada 1.300 lotes. Solo si el volumen
        // supera este límite se recorta y se avisa en el PDF que use la versión Excel.
        $limitePdf = 2500;
        $totalItems = (clone $query)->count();
        $isTruncated = $totalItems > $limitePdf;

        $inventories = $query->with(['aisle', 'shelf'])
            ->when($isTruncated, fn ($q) => $q->limit($limitePdf))
            ->get();

        $pdf = Pdf::loadView('reports.pdf', compact('inventories', 'totalItems', 'isTruncated'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('reporte_inventario_'.date('Ymd_His').'.pdf');
    }
}
