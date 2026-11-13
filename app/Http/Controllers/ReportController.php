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
    // construye la consulta base con los filtros aplicados
    private function buildInventoryQuery(Request $request)
    {
        $query = Inventory::where('estado', 'Disponible');

        // los controlados solo aparecen si el usuario tiene permiso para verlos
        $user = auth()->user();
        $puedeVerControlados = $user && ($user->hasRole('Administrador') || $user->can('ver.despacho_controlado'));

        if (! $puedeVerControlados) {
            $query->whereNotIn('categoria', ['Fármacos Controlados', 'Controlado', 'Estupefacientes']);
        }

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

        // la categoria se compara tal cual viene escrita en los datos
        if ($request->filled('categoria')) {
            $cat = trim((string) $request->input('categoria'));
            $esCatControlada = InventoryService::clasificarGrupoProducto('', $cat) === 'Estupefacientes';

            if (! $esCatControlada || $puedeVerControlados) {
                $query->where('categoria', $cat);
            }
        }

        if ($request->filled('pasillo')) {
            $query->where('pasillo', $request->input('pasillo'));
        }

        if ($request->filled('programa')) {
            $query->where('programa', $request->input('programa'));
        }

        if ($request->filled('estante')) {
            $query->where('estante', $request->input('estante'));
        }

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
        // el inventario completo no cabe en los 128 mb que trae php por defecto, asi que se amplia el limite solo mientras dura esta descarga
        @ini_set('memory_limit', '1024M');
        @set_time_limit(300);

        $query = $this->buildInventoryQuery($request);

        // si aun asi son demasiados lotes, el pdf se recorta y avisa que descarguen el excel
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
