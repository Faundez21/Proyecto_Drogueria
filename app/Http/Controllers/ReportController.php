<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Iniciar la consulta
        $query = Inventory::query();

        //esto hace de que no se muestre los productos en cuarentena
        $query = Inventory::where(function ($q) {
            $q->where('estado', '!=', 'Cuarentena')
                ->orWhereNull('estado');
        });
        // Cargar relaciones si están definidas en el modelo
        $relations = [];
        if (method_exists(Inventory::class, 'producto')) {
            $relations[] = 'producto';
        } elseif (method_exists(Inventory::class, 'product')) {
            $relations[] = 'product';
        }

        if (method_exists(Inventory::class, 'pasillo')) {
            $relations[] = 'pasillo';
        } elseif (method_exists(Inventory::class, 'aisle')) {
            $relations[] = 'aisle';
        }

        if (method_exists(Inventory::class, 'estante')) {
            $relations[] = 'estante';
        } elseif (method_exists(Inventory::class, 'shelf')) {
            $relations[] = 'shelf';
        }

        if (! empty($relations)) {
            $query->with($relations);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('producto', 'like', "%{$search}%")
                    ->orWhere('lote', 'like', "%{$search}%")
                    ->orWhere('categoria', 'like', "%{$search}%");

                if (method_exists(Inventory::class, 'producto')) {
                    $q->orWhereHas('producto', fn ($qp) => $qp->where('nombre', 'like', "%{$search}%"));
                } elseif (method_exists(Inventory::class, 'product')) {
                    $q->orWhereHas('product', fn ($qp) => $qp->where('nombre', 'like', "%{$search}%"));
                }
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->input('categoria'));
        }

        if ($request->filled('pasillo')) {
            $pasillo = $request->input('pasillo');
            $query->where(function ($q) use ($pasillo) {
                $q->where('pasillo_nombre', $pasillo)
                    ->orWhere('pasillo', $pasillo);
            });
        }

        if ($request->filled('estante')) {
            $estante = $request->input('estante');
            $query->where(function ($q) use ($estante) {
                $q->where('estanteria_nombre', $estante)
                    ->orWhere('estante', $estante)
                    ->orWhere('estanteria', $estante);
            });
        }

        $orderBy = $request->input('order_by', 'recent');
        switch ($orderBy) {
            case 'nombre_asc':
                $query->orderBy('producto', 'asc');
                break;
            case 'stock_desc':
                if (\Schema::hasColumn('inventories', 'stock_total')) {
                    $query->orderBy('stock_total', 'desc');
                } else {
                    $query->orderBy('cantidad', 'desc');
                }
                break;
            case 'stock_asc':
                if (\Schema::hasColumn('inventories', 'stock_total')) {
                    $query->orderBy('stock_total', 'asc');
                } else {
                    $query->orderBy('cantidad', 'asc');
                }
                break;
            case 'vencimiento':
                $query->orderBy('fecha_vencimiento', 'asc');
                break;
            case 'recent':
            default:
                $query->latest();
                break;
        }

        $productosLista = Inventory::where(function ($q) {
            $q->where('estado', '!=', 'Cuarentena')
                ->orWhereNull('estado');
        })
            ->pluck('producto')
            ->filter()
            ->unique()
            ->sort()
            ->values();


        $inventories = $query->latest()->paginate(10)->withQueryString();
        return view('reportes.index', compact('inventories', 'productosLista'));
    }
}
