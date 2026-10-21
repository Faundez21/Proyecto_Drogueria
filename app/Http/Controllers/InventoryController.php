<?php

namespace App\Http\Controllers;

use App\Imports\InventoriesImport;
use App\Models\Inventory;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class InventoryController extends Controller
{
    /**
     * Muestra la lista de inventario principal con sus filtros y búsqueda.
     */
    public function index(Request $request)
    {
        $query = Inventory::query();
        $query->where('estado', 'Disponible');
        // Solo el rol Administrador puede ver los productos de la categoría "Fármacos Controlados"
        if (! auth()->user()->hasRole('Administrador')) {
            $query->where('categoria', '!=', 'Fármacos Controlados');
        } else {
            if ($request->input('categoria') === 'Fármacos Controlados') {
                $query->where('categoria', 'Fármacos Controlados');
            }
        }
        if ($request->filled('categoria') && $request->input('categoria') !== 'Fármacos Controlados') {
            $query->where('categoria', $request->categoria);
        }
        // 1. Filtro de Búsqueda Global (Producto, Lote, Categoría)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('producto', 'LIKE', "%{$search}%")
                    ->orWhere('lote', 'LIKE', "%{$search}%")
                    ->orWhere('categoria', 'LIKE', "%{$search}%")
                    ->orWhere('rut_proveedor', 'LIKE', "%{$search}%");
            });
        }

        // 2. Filtros Específicos
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
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

        // 3. Ordenamiento Dinámico
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
                    $query->latest();
                    break;
            }
        } else {
            $query->latest(); // Por defecto los más recientes primero
        }

        // 4. Ejecución de la consulta con paginación
        $inventories = $query->paginate(15)->withQueryString();

        // 5. Generar lista de productos únicos para el autocompletado de la vista
        $productosLista = Inventory::select('producto')
            ->distinct()
            ->orderBy('producto', 'asc')
            ->pluck('producto');

        // 6. Programas disponibles para el filtro
        $programasLista = Inventory::whereNotNull('programa')
            ->where('programa', '!=', '')
            ->distinct()
            ->orderBy('programa', 'asc')
            ->pluck('programa');
        $pasillosLista = Inventory::whereNotNull('pasillo')
            ->where('pasillo', '!=', '')
            ->distinct()
            ->orderBy('pasillo', 'asc')
            ->pluck('pasillo');

        $estantesLista = Inventory::whereNotNull('estante')
            ->where('estante', '!=', '')
            ->distinct()
            ->orderBy('estante', 'asc')
            ->pluck('estante');

        // Agregar las listas de Nivel y Posición que faltaban
        $nivelesLista = Inventory::whereNotNull('nivel')
            ->where('nivel', '!=', '')
            ->distinct()
            ->orderBy('nivel', 'asc')
            ->pluck('nivel');

        $posicionesLista = Inventory::whereNotNull('posicion')
            ->where('posicion', '!=', '')
            ->distinct()
            ->orderBy('posicion', 'asc')
            ->pluck('posicion');

        return view('inventario.index', compact(
            'inventories',
            'productosLista',
            'programasLista',
            'pasillosLista',
            'estantesLista',
            'nivelesLista',
            'posicionesLista'
        ));
    }

    /**
     * Guarda un ingreso manual desde el Modal "Ingreso Directo de Producto".
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_recepcion' => 'required|date',
            'tipo_documento' => 'required|string|max:255',
            'numero_documento' => 'required|string|max:255',
            'orden_compra' => 'nullable|string|max:255',
            'origen_mercaderia' => 'nullable|string|max:255',
            'rut_proveedor' => 'nullable|string|max:20',
            'razon_social' => 'nullable|string|max:255',
            'producto' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'estado' => 'required|in:Disponible,Cuarentena,Merma',
            'motivo' => 'nullable|string|max:1000',
            'lote' => 'nullable|string|max:255',
            'fecha_vencimiento' => 'nullable|date',
            'cantidad' => 'required|integer|min:1',
            'valor_neto_unitario' => 'nullable|numeric|min:0',
            'pasillo' => 'nullable|string|max:50',
            'estante' => 'nullable|string|max:50',
            'programa' => 'nullable|string|max:255',
            'nivel' => 'nullable|string|max:100',
            'posicion' => 'nullable|string|max:100',
            'solicitud_pedido_mensual' => 'nullable|boolean',
            'solicitud_stock_critico' => 'nullable|boolean',
            'adelanto_de_pedido' => 'nullable|boolean',
            'traspaso' => 'nullable|boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['lote'] = ($validated['lote'] ?? '') ?: 'S/L';
        $validated['product_id'] = InventoryService::productoId($validated['producto']);

        if (!isset($validated['valor_neto_unitario']) || $validated['valor_neto_unitario'] === '' || $validated['valor_neto_unitario'] === null) {
            $validated['valor_neto_unitario'] = 0;
        }

        foreach ($validated as $key => $value) {
            if ($value === '') {
                $validated[$key] = null;
            }
        }

        foreach (['solicitud_pedido_mensual', 'solicitud_stock_critico', 'adelanto_de_pedido', 'traspaso'] as $flag) {
            $validated[$flag] = $request->boolean($flag);
        }

        Inventory::create($validated);

        return redirect()->back()->with('success', 'Producto ingresado exitosamente a bodega.');

        return redirect()->back()->with('success', 'Producto ingresado exitosamente a bodega.');
    }

    /**
     * Actualiza el estado (Mandar a Cuarentena, Merma o Disponible) desde el Modal.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'estado' => 'required|string',
        ]);

        $inventory = Inventory::findOrFail($id);

        $data = $request->only(['estado', 'motivo']);

        $data['user_id'] = Auth::id();

        $inventory->update($data);

        // Mensaje dinámico según el estado seleccionado
        if ($data['estado'] == 'Cuarentena') {
            $mensaje = 'El lote ha sido bloqueado y enviado a cuarentena.';
        } elseif ($data['estado'] == 'Merma') {
            $mensaje = 'El lote ha sido declarado como Merma.';
        } else {
            $mensaje = 'El lote ha sido liberado exitosamente.';
        }

        return redirect()->back()->with('success', $mensaje);
    }

    /**
     * Procesa el archivo Excel para carga masiva.
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            $import = new InventoriesImport(Auth::id());
            Excel::import($import, $request->file('excel'));

            $total = $import->importados();

            return redirect()->back()->with('success', "Carga masiva procesada: {$total} registro(s) de inventario importado(s).");
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (ValidationException $e) {
            $primerError = collect($e->failures())->first();
            $fila = $primerError ? $primerError->row() + 1 : '?';
            $mensaje = $primerError ? $primerError->errors()[0] : 'Archivo inválido.';

            return redirect()->back()->with('error', "Fila {$fila}: {$mensaje}");
        } catch (\Throwable $e) {
            \Log::error('Error en carga masiva de inventario: '.$e->getMessage());

            return redirect()->back()->with('error', 'Hubo un error al procesar el archivo. Verifique el formato de la plantilla.');
        }
    }

    /**
     * Descarga la plantilla oficial de carga masiva.
     */
    public function template()
    {
        $filePath = public_path('plantillas/plantilla_inventario.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return redirect()->back()->with('warning', 'El archivo de plantilla aún no está disponible en el servidor.');
    }
}
