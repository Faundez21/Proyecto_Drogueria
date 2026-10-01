<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
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
        $query = Inventory::where(function ($q) {
            $q->where('estado', '!=', 'Cuarentena')
                ->orWhereNull('estado');
        });

        // Cargar relaciones si están definidas en el modelo
        $relations = [];
        if (method_exists(Inventory::class, 'producto')) $relations[] = 'producto';
        elseif (method_exists(Inventory::class, 'product')) $relations[] = 'product';

        if (method_exists(Inventory::class, 'pasillo')) $relations[] = 'pasillo';
        elseif (method_exists(Inventory::class, 'aisle')) $relations[] = 'aisle';

        if (method_exists(Inventory::class, 'estante')) $relations[] = 'estante';
        elseif (method_exists(Inventory::class, 'shelf')) $relations[] = 'shelf';

        if (!empty($relations)) {
            $query->with($relations);
        }

        // 1. Filtro de Búsqueda (Texto general)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                if (Schema::hasColumn('inventories', 'producto')) $q->orWhere('producto', 'like', "%{$search}%");
                if (Schema::hasColumn('inventories', 'lote')) $q->orWhere('lote', 'like', "%{$search}%");
                if (Schema::hasColumn('inventories', 'categoria')) $q->orWhere('categoria', 'like', "%{$search}%");

                if (method_exists(Inventory::class, 'producto')) {
                    $q->orWhereHas('producto', fn ($qp) => $qp->where('nombre', 'like', "%{$search}%"));
                } elseif (method_exists(Inventory::class, 'product')) {
                    $q->orWhereHas('product', fn ($qp) => $qp->where('nombre', 'like', "%{$search}%"));
                }
            });
        }

        // 2. Filtro de Categoría
        if ($request->filled('categoria')) {
            if (Schema::hasColumn('inventories', 'categoria')) {
                $query->where('categoria', $request->input('categoria'));
            }
        }

        // 3. Filtro de Pasillo
        if ($request->filled('pasillo')) {
            $pasillo = $request->input('pasillo');
            $query->where(function ($q) use ($pasillo) {
                if (method_exists(Inventory::class, 'pasillo')) {
                    $q->orWhereHas('pasillo', fn($qp) => $qp->where('nombre', $pasillo));
                } elseif (method_exists(Inventory::class, 'aisle')) {
                    $q->orWhereHas('aisle', fn($qp) => $qp->where('nombre', $pasillo));
                }

                if (Schema::hasColumn('inventories', 'pasillo_nombre')) $q->orWhere('pasillo_nombre', $pasillo);
                if (Schema::hasColumn('inventories', 'pasillo')) $q->orWhere('pasillo', $pasillo);
            });
        }

        // 4. Filtro de Estante
        if ($request->filled('estante')) {
            $estante = $request->input('estante');
            $query->where(function ($q) use ($estante) {
                if (method_exists(Inventory::class, 'estante')) {
                    $q->orWhereHas('estante', fn($qe) => $qe->where('nombre', $estante));
                } elseif (method_exists(Inventory::class, 'shelf')) {
                    $q->orWhereHas('shelf', fn($qe) => $qe->where('nombre', $estante));
                }

                if (Schema::hasColumn('inventories', 'estanteria_nombre')) $q->orWhere('estanteria_nombre', $estante);
                if (Schema::hasColumn('inventories', 'estante')) $q->orWhere('estante', $estante);
                if (Schema::hasColumn('inventories', 'estanteria')) $q->orWhere('estanteria', $estante);
            });
        }

        // 5. Ordenamiento
        $orderBy = $request->input('order_by', 'recent');
        switch ($orderBy) {
            case 'nombre_asc':
                if (Schema::hasColumn('inventories', 'producto')) $query->orderBy('producto', 'asc');
                break;
            case 'stock_desc':
                if (Schema::hasColumn('inventories', 'stock_total')) $query->orderBy('stock_total', 'desc');
                elseif (Schema::hasColumn('inventories', 'cantidad')) $query->orderBy('cantidad', 'desc');
                break;
            case 'stock_asc':
                if (Schema::hasColumn('inventories', 'stock_total')) $query->orderBy('stock_total', 'asc');
                elseif (Schema::hasColumn('inventories', 'cantidad')) $query->orderBy('cantidad', 'asc');
                break;
            case 'vencimiento':
                if (Schema::hasColumn('inventories', 'fecha_vencimiento')) $query->orderBy('fecha_vencimiento', 'asc');
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

        return view('reportes.index', compact('inventories'));
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

        $pdf = Pdf::loadView('reportes.pdf', compact('inventories'));
        $pdf->setPaper('A4', 'landscape'); // Formato horizontal para que quepan las columnas

        return $pdf->download('reporte_inventario_'.date('Ymd_His').'.pdf');
    }
}
