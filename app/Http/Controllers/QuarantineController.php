<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class QuarantineController extends Controller
{
    public function index(Request $request)
    {
        // 1. Consulta Principal para el Directorio Físico
        $query = Inventory::with(['product', 'user'])->where('estado', 'Cuarentena');

        $user = auth()->user();
        $puedeVerControlados = $user && ($user->hasRole('Administrador') || $user->can('ver.despacho_controlado'));
        $controladosLista = InventoryService::controlados();

        if (! $puedeVerControlados) {
            $query->where('categoria', '!=', 'Fármacos Controlados');
        }

        // Filtro de Búsqueda Global
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('producto', 'LIKE', "%{$search}%")
                    ->orWhere('lote', 'LIKE', "%{$search}%")
                    ->orWhere('categoria', 'LIKE', "%{$search}%");
            });
        }

        // Filtros Específicos Avanzados
        if ($request->filled('categoria')) {
            $catFiltro = trim($request->input('categoria'));
            // La categoría se compara tal cual viene en los datos; no hay equivalencias fijas en código.
            $esControlada = InventoryService::clasificarGrupoProducto('', $catFiltro) === 'Estupefacientes';
            if (! $esControlada || $puedeVerControlados) {
                $query->where('categoria', $catFiltro);
            }
        }
        if ($request->filled('pasillo')) {
            $query->where('pasillo', $request->pasillo);
        }
        if ($request->filled('estante')) {
            $query->where('estante', $request->estante);
        }
        if ($request->filled('programa')) {
            $query->where('programa', $request->programa);
        }

        // Ordenamiento Dinámico
        if ($request->filled('order_by')) {
            switch ($request->order_by) {
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
                    $query->orderBy('fecha_vencimiento', 'asc');
                    break;
                case 'recent':
                default:
                    $query->latest('updated_at');
                    break;
            }
        } else {
            $query->latest('updated_at'); // Por defecto
        }

        // KPIs globales sobre el total filtrado
        $totalLotesGlobal = (clone $query)->count();
        $totalUnidadesGlobal = (int) (clone $query)->sum('cantidad');
        $valorTotalRetenido = (float) ((clone $query)->selectRaw('SUM(cantidad * valor_neto_unitario) as total')->value('total') ?? 0);

        $inventories = $query->paginate(15)->withQueryString();

        // 2. Consulta para el Historial Avanzado (Sin Paginación)
        $historial = StockMovement::with('user')
            ->when(! $puedeVerControlados, fn ($q) => $q->whereNotIn('producto', $controladosLista))
            ->where(function ($q) {
                $q->where('estado_lote', 'Cuarentena')
                    ->orWhere('origen_destino', 'LIKE', '%Cuarentena%');
            })
            ->orderBy('fecha_hora', 'desc')
            ->limit(200)
            ->get();

        if ($historial->isEmpty()) {
            $historial = Inventory::with('user')
                ->where('estado', 'Cuarentena')
                ->when(! $puedeVerControlados, fn ($q) => $q->where('categoria', '!=', 'Fármacos Controlados'))
                ->orderBy('updated_at', 'desc')
                ->limit(200)
                ->get();
        }

        // 3. Generar listas para los Dropdowns de Filtros y Modales
        $baseDropdownQuery = Inventory::where('estado', 'Cuarentena')
            ->when(! $puedeVerControlados, fn ($q) => $q->whereNotIn('categoria', ['Fármacos Controlados', 'Estupefacientes']));

        $productosLista = (clone $baseDropdownQuery)->select('producto')->distinct()->orderBy('producto', 'asc')->pluck('producto');
        $programasLista = InventoryService::programasLista();
        $categoriasLista = InventoryService::categoriasLista($puedeVerControlados);

        $datosDist = InventoryService::datosDistribucionFisica();
        $pasillosLista = $datosDist['pasillosLista'];
        $estantesLista = $datosDist['estantesLista'];
        $nivelesLista = $datosDist['nivelesLista'];
        $posicionesLista = $datosDist['posicionesLista'];
        $distribucionJerarquia = $datosDist['distribucionJerarquia'];
        $ubicacionesOcupadas = $datosDist['ubicacionesOcupadas'];

        return view('quarantine.index', compact(
            'inventories',
            'historial',
            'productosLista',
            'programasLista',
            'categoriasLista',
            'pasillosLista',
            'estantesLista',
            'nivelesLista',
            'posicionesLista',
            'distribucionJerarquia',
            'ubicacionesOcupadas',
            'totalLotesGlobal',
            'totalUnidadesGlobal',
            'valorTotalRetenido'
        ));
    }

    public function update(Request $request, string $id)
    {
   
    }
}