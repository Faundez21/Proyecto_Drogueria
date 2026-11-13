<?php

namespace App\Http\Controllers;

use App\Imports\InventoriesImport;
use App\Models\Aisle;
use App\Models\Inventory;
use App\Models\Level;
use App\Models\Position;
use App\Models\Product;
use App\Models\Shelf;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryController extends Controller
{
    // muestra la lista de inventario principal con sus filtros y busqueda
    public function index(Request $request)
    {
        $query = Inventory::query();

        // el directorio principal solo muestra productos disponibles
        $query->where('estado', 'Disponible');

        // solo el rol administrador o quien tenga ver.despacho_controlado puede ver los productos de la categoria "farmacos controlados"
        $puedeVerControlados = auth()->user()->hasRole('Administrador') || auth()->user()->can('ver.despacho_controlado');

        if (! $puedeVerControlados) {
            $query->whereNotIn('categoria', ['Fármacos Controlados', 'Estupefacientes']);
        }

        // busqueda por producto, lote o categoria
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('producto', 'LIKE', "%{$search}%")
                    ->orWhere('lote', 'LIKE', "%{$search}%")
                    ->orWhere('categoria', 'LIKE', "%{$search}%")
                    ->orWhere('programa', 'LIKE', "%{$search}%")
                    ->orWhere('rut_proveedor', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('categoria')) {
            $catFiltro = trim($request->input('categoria'));
            // la categoria se compara tal cual viene en los datos; no hay equivalencias fijas en codigo
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
            $query->latest();
        }

        $inventories = $query->paginate(15)->withQueryString();

        // datos que usan los modales, independientes de los filtros de la tabla
        $mermas = Inventory::with('user')
            ->where('estado', 'Merma')
            ->when(! $puedeVerControlados, fn ($q) => $q->whereNotIn('categoria', ['Fármacos Controlados', 'Estupefacientes']))
            ->orderBy('updated_at', 'desc')
            ->get();
        $historial = Inventory::with('user')
            ->when(! $puedeVerControlados, fn ($q) => $q->whereNotIn('categoria', ['Fármacos Controlados', 'Estupefacientes']))
            ->orderBy('created_at', 'desc')
            ->limit(150)
            ->get();

        // productos unicos, juntando el catalogo maestro con lo que hay en inventario
        InventoryService::sincronizarProductosDesdeInventario();
        $productosInv = Inventory::select('producto')
            ->whereNotNull('producto')
            ->where('producto', '!=', '')
            ->distinct()
            ->pluck('producto');
        $productosCat = Product::orderBy('nombre')->pluck('nombre');
        $productosLista = $productosInv->merge($productosCat)->unique()->sort(SORT_NATURAL)->values();

        // listas de los filtros y formularios: mantenedores de bodega mas lo que ya esta en inventario
        $programasLista = InventoryService::programasLista();
        $categoriasLista = InventoryService::categoriasLista($puedeVerControlados);

        $datosDist = InventoryService::datosDistribucionFisica();
        $pasillosLista = $datosDist['pasillosLista'];
        $estantesLista = $datosDist['estantesLista'];
        $nivelesLista = $datosDist['nivelesLista'];
        $posicionesLista = $datosDist['posicionesLista'];
        $distribucionJerarquia = $datosDist['distribucionJerarquia'];
        $ubicacionesOcupadas = $datosDist['ubicacionesOcupadas'];

        $proveedoresActivos = \App\Models\Provider::where('status', 'activo')
            ->orderBy('name')
            ->get(['id', 'name', 'rut']);

        return view('inventory.index', compact(
            'inventories',
            'mermas',
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
            'proveedoresActivos'
        ));
    }

    // guarda un ingreso manual desde el modal "ingreso directo de producto"
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'correlativo_interno' => 'nullable|integer|min:1',
            'fecha_recepcion' => 'required|date|before_or_equal:today',
            'tipo_documento' => 'required|string|max:255',
            'numero_documento' => 'required|string|max:255',
            'orden_compra' => 'nullable|string|max:255',
            'origen_mercaderia' => 'nullable|string|max:500',
            'rut_proveedor' => 'nullable|string|max:30',
            'razon_social' => 'nullable|string|max:500',
            'producto' => 'required|string|max:1000',
            'categoria' => 'required|string|max:500',
            'estado' => 'required|in:Disponible,Cuarentena,Merma',
            'motivo' => 'nullable|string|max:1000',
            'lote' => 'nullable|string|max:255',
            'fecha_vencimiento' => 'nullable|date',
            'cantidad' => 'required|integer|min:1',
            'valor_neto_unitario' => 'nullable|integer|min:0',
            'pasillo' => 'nullable|string|max:50',
            'estante' => 'nullable|string|max:50',
            'programa' => 'nullable|string|max:500',
            'nivel' => 'nullable|string|max:100',
            'posicion' => 'nullable|string|max:100',
            'solicitud_pedido_mensual' => 'nullable|boolean',
            'solicitud_stock_critico' => 'nullable|boolean',
            'adelanto_de_pedido' => 'nullable|boolean',
            'traspaso' => 'nullable|boolean',
        ], [
            'fecha_recepcion.required' => 'La fecha de recepción es obligatoria.',
            'tipo_documento.required' => 'Seleccione el tipo de documento de respaldo.',
            'numero_documento.required' => 'Ingrese el número o folio del documento.',
            'producto.required' => 'El nombre del producto es obligatorio.',
            'categoria.required' => 'Seleccione una categoría válida.',
            'estado.required' => 'Seleccione el estado inicial del lote.',
            'cantidad.required' => 'La cantidad a ingresar es obligatoria.',
            'cantidad.min' => 'La cantidad debe ser al menos 1 unidad.',
            'valor_neto_unitario.integer' => 'El Valor Neto Unitario debe ser un número entero sin decimales ($ CLP).',
            'fecha_recepcion.before_or_equal' => 'La fecha de recepción no puede ser posterior a hoy.',
        ]);

        // un lote vencido solo puede ingresarse directamente como merma
        $validator->after(function ($v) use ($request) {
            $vence = $request->input('fecha_vencimiento');
            if (! empty($vence) && $request->input('estado') !== 'Merma') {
                try {
                    if (\Carbon\Carbon::parse($vence)->startOfDay()->lt(now()->startOfDay())) {
                        $v->errors()->add('fecha_vencimiento', 'El lote está vencido. Solo puede ingresarse con estado Merma.');
                    }
                } catch (\Throwable $e) {
                }
            }

            $errorUbicacion = InventoryService::validarUbicacion(
                $request->input('pasillo'),
                $request->input('estante'),
                $request->input('nivel'),
                $request->input('posicion')
            );
            if ($errorUbicacion) {
                $v->errors()->add('estante', $errorUbicacion);
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'deposit')
                ->with('error', $validator->errors()->first() ?: 'Por favor revise los campos obligatorios en el formulario de Ingreso Manual.');
        }

        $validated = $validator->validated();

        if (($validated['estado'] ?? 'Disponible') !== 'Merma' && ! empty($validated['estante']) && ! empty($validated['nivel'])) {
            $ocupante = InventoryService::verificarUbicacionOcupada(
                $validated['estante'] ?? null,
                $validated['nivel'] ?? null,
                $validated['posicion'] ?? null,
                null,
                $validated['producto'] ?? null,
                $validated['lote'] ?? null
            );

            if ($ocupante) {
                $detallePos = ! empty($validated['posicion']) ? ", Posición {$validated['posicion']}" : '';
                $msgOcupado = "La ubicación seleccionada (Estante {$validated['estante']}, Nivel {$validated['nivel']}{$detallePos}) ya se encuentra ocupada por «{$ocupante->producto}» (Lote: " . ($ocupante->lote ?: 'S/L') . ").";

                return redirect()->back()
                    ->withInput()
                    ->with('open_modal', 'deposit')
                    ->with('error', $msgOcupado);
            }
        }

        // capacidad del estante (en unidades); capacidad 0 = sin limite
        if (($validated['estado'] ?? 'Disponible') !== 'Merma' && ! empty($validated['estante'])) {
            $msgCapacidad = InventoryService::verificarCapacidadEstante($validated['estante'], (int) ($validated['cantidad'] ?? 0));
            if ($msgCapacidad) {
                return redirect()->back()
                    ->withInput()
                    ->with('open_modal', 'deposit')
                    ->with('error', $msgCapacidad);
            }
        }

        try {
            $inventory = DB::transaction(function () use ($validated, $request) {
                $validated['user_id'] = Auth::id();
                $validated['producto'] = trim($validated['producto']);
                $validated['lote'] = isset($validated['lote']) && trim((string) $validated['lote']) !== ''
                    ? trim((string) $validated['lote'])
                    : 'S/L';
                $validated['correlativo_interno'] = isset($validated['correlativo_interno']) && trim((string) $validated['correlativo_interno']) !== ''
                    ? (int) $validated['correlativo_interno']
                    : (((int) Inventory::max('correlativo_interno')) + 1);
                $validated['product_id'] = InventoryService::productoId($validated['producto']);

                if (! isset($validated['valor_neto_unitario']) || $validated['valor_neto_unitario'] === '' || $validated['valor_neto_unitario'] === null) {
                    $validated['valor_neto_unitario'] = 0;
                }

                foreach ($validated as $key => $value) {
                    if ($value === '') {
                        $validated[$key] = null;
                    }
                }

                $validated['lote'] = $validated['lote'] ?? 'S/L';
                $validated['valor_neto_unitario'] = $validated['valor_neto_unitario'] ?? 0;

                foreach (['solicitud_pedido_mensual', 'solicitud_stock_critico', 'adelanto_de_pedido', 'traspaso'] as $flag) {
                    $validated[$flag] = $request->boolean($flag);
                }

                $validated = InventoryService::resolverRelaciones($validated);

                // si ya existe exactamente el mismo producto + lote + estado, acumulamos stock o creamos registro limpio
                $existente = Inventory::where('producto', $validated['producto'])
                    ->where('lote', $validated['lote'])
                    ->where('estado', $validated['estado'])
                    ->first();

                if ($existente) {
                    $existente->increment('cantidad', (int) $validated['cantidad']);
                    $existente->update(array_filter([
                        'correlativo_interno' => $validated['correlativo_interno'],
                        'fecha_recepcion' => $validated['fecha_recepcion'],
                        'tipo_documento' => $validated['tipo_documento'],
                        'numero_documento' => $validated['numero_documento'],
                        'orden_compra' => $validated['orden_compra'] ?? $existente->orden_compra,
                        'origen_mercaderia' => $validated['origen_mercaderia'] ?? $existente->origen_mercaderia,
                        'rut_proveedor' => $validated['rut_proveedor'] ?? $existente->rut_proveedor,
                        'razon_social' => $validated['razon_social'] ?? $existente->razon_social,
                        'valor_neto_unitario' => $validated['valor_neto_unitario'] ?: $existente->valor_neto_unitario,
                        'pasillo' => $validated['pasillo'] ?? $existente->pasillo,
                        'estante' => $validated['estante'] ?? $existente->estante,
                        'nivel' => $validated['nivel'] ?? $existente->nivel,
                        'posicion' => $validated['posicion'] ?? $existente->posicion,
                        'programa' => $validated['programa'] ?? $existente->programa,
                        'position_id' => $validated['position_id'] ?? $existente->position_id,
                        'provider_id' => $validated['provider_id'] ?? $existente->provider_id,
                    ], fn ($v) => $v !== null));
                    $inv = $existente->fresh();
                } else {
                    $inv = Inventory::create($validated);
                }

                InventoryService::registrarMovimiento($inv, [
                    'user_id' => Auth::id(),
                    'tipo_movimiento' => 'Recepción',
                    'cantidad' => (int) $validated['cantidad'],
                    'fecha_hora' => now(),
                    'origen_destino' => 'Desde: ' . ($inv->razon_social ?? $inv->origen_mercaderia ?? 'Ingreso Manual Directo'),
                    'documento_respaldo' => ($inv->tipo_documento ?? 'Doc') . ' #' . ($inv->numero_documento ?? 'S/N'),
                    'estado_lote' => $inv->estado,
                ]);

                return $inv;
            });

            $msgExtra = $inventory->estado === 'Cuarentena'
                ? ' (Enviado al módulo de Cuarentena para revisión técnica).'
                : ($inventory->estado === 'Merma' ? ' (Registrado en el módulo de Mermas).' : ' y disponible en el directorio principal.');

            return redirect()->back()->with('success', "Producto «{$inventory->producto}» (Lote: {$inventory->lote}) ingresado exitosamente{$msgExtra}");
        } catch (\Throwable $e) {
            \Log::error('Error en ingreso manual de inventario: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('open_modal', 'deposit')
                ->with('error', 'Error al guardar el ingreso manual: ' . $e->getMessage());
        }
    }

    // actualiza el estado (mandar a cuarentena, merma o disponible) o reasigna ubicacion fisica desde el modal
    public function update(Request $request, string $id)
    {
        $request->validate([
            'estado' => 'required|in:Cuarentena,Disponible,Merma',
            'motivo' => 'nullable|string|max:1000',
            'pasillo' => 'nullable|string|max:50',
            'estante' => 'nullable|string|max:50',
            'nivel' => 'nullable|string|max:100',
            'posicion' => 'nullable|string|max:100',
        ]);

        $inventory = Inventory::findOrFail($id);

        $rechazar = function (string $mensaje, int $codigo = 422) use ($request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'message' => $mensaje], $codigo);
            }

            return redirect()->back()->with('error', $mensaje);
        };

        // un farmaco controlado solo lo modifica quien puede verlos
        $usuario = $request->user();
        $puedeVerControlados = $usuario && ($usuario->hasRole('Administrador') || $usuario->can('ver.despacho_controlado'));
        if (! $puedeVerControlados && InventoryService::esControlado($inventory->producto)) {
            return $rechazar('No tiene permiso para modificar fármacos controlados.', 403);
        }

        // cuarentena y merma necesitan un motivo, y un lote vencido no puede volver a disponible
        $estadoNuevo = $request->input('estado');
        if ($estadoNuevo !== $inventory->estado && in_array($estadoNuevo, ['Cuarentena', 'Merma'], true) && trim((string) $request->input('motivo')) === '') {
            return $rechazar("Indique el motivo para pasar el lote a {$estadoNuevo}.");
        }
        if ($estadoNuevo === 'Disponible' && $inventory->fecha_vencimiento && strtotime($inventory->fecha_vencimiento) < strtotime(date('Y-m-d'))) {
            return $rechazar('El lote está vencido: no puede volver a Disponible.');
        }

        $estadoAnterior = $inventory->estado;
        $pasilloAnterior = $inventory->pasillo;
        $estanteAnterior = $inventory->estante;
        $nivelAnterior = $inventory->nivel;
        $posicionAnterior = $inventory->posicion;

        $data = $request->only(['estado', 'motivo']);

        // si el pasillo no cambio y el estante viene vacio se conserva la ubicacion actual del lote
        $pasilloIgual = mb_strtolower(trim((string) $request->input('pasillo'))) === mb_strtolower(trim((string) $inventory->pasillo));
        $tieneEstanteActual = ! empty($inventory->estante) && strtoupper((string) $inventory->estante) !== 'P.A';
        if ($request->has('pasillo') && $pasilloIgual && ! $request->filled('estante') && $tieneEstanteActual) {
            $request->request->remove('pasillo');
            $request->request->remove('estante');
            $request->request->remove('nivel');
            $request->request->remove('posicion');
            $request->query->remove('pasillo');
            $request->query->remove('estante');
            $request->query->remove('nivel');
            $request->query->remove('posicion');
            $request->json()?->remove('pasillo');
            $request->json()?->remove('estante');
            $request->json()?->remove('nivel');
            $request->json()?->remove('posicion');
        }

        // si se indica una ubicacion nueva, debe existir en el mapa de bodega
        if ($request->filled('estante') || ($request->filled('pasillo') && ! $pasilloIgual)) {
            $errorUbicacion = InventoryService::validarUbicacion(
                $request->input('pasillo'),
                $request->input('estante'),
                $request->input('nivel'),
                $request->input('posicion')
            );
            if ($errorUbicacion) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['ok' => false, 'message' => $errorUbicacion], 422);
                }

                return redirect()->back()->with('error', $errorUbicacion);
            }
        }

        if ($request->has('pasillo')) {
            $data['pasillo'] = $request->filled('pasillo') ? trim($request->pasillo) : 'P.A';
        }
        if ($request->has('estante')) {
            $data['estante'] = $request->filled('estante') ? trim($request->estante) : 'P.A';
        }
        if ($request->has('nivel')) {
            $data['nivel'] = $request->filled('nivel') ? trim($request->nivel) : null;
        }
        if ($request->has('posicion')) {
            $data['posicion'] = $request->filled('posicion') ? trim($request->posicion) : null;
        }

        $estanteEvaluar = $data['estante'] ?? $inventory->estante;
        $nivelEvaluar = array_key_exists('nivel', $data) ? $data['nivel'] : $inventory->nivel;
        $posicionEvaluar = array_key_exists('posicion', $data) ? $data['posicion'] : $inventory->posicion;

        if (($data['estado'] ?? $inventory->estado) !== 'Merma' && ! empty($estanteEvaluar) && $estanteEvaluar !== 'P.A' && ! empty($nivelEvaluar)) {
            $ocupante = InventoryService::verificarUbicacionOcupada(
                $estanteEvaluar,
                $nivelEvaluar,
                $posicionEvaluar,
                (int) $inventory->id
            );

            if ($ocupante) {
                $detallePos = ! empty($posicionEvaluar) ? ", Posición {$posicionEvaluar}" : '';
                $msgOcupado = "La ubicación seleccionada (Estante {$estanteEvaluar}, Nivel {$nivelEvaluar}{$detallePos}) ya se encuentra en uso por «{$ocupante->producto}» (Lote: " . ($ocupante->lote ?: 'S/L') . "). Seleccione otra ubicación disponible.";

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'ok' => false,
                        'message' => $msgOcupado,
                    ], 422);
                }

                return redirect()->back()->with('error', $msgOcupado);
            }
        }

        // si el lote cambia de estante, el estante nuevo debe tener capacidad para sus unidades
        $estanteNuevo = trim((string) ($data['estante'] ?? $inventory->estante));
        $cambiaDeEstante = mb_strtolower($estanteNuevo) !== mb_strtolower(trim((string) $inventory->estante));
        if ($cambiaDeEstante && ($data['estado'] ?? $inventory->estado) !== 'Merma') {
            $msgCapacidad = InventoryService::verificarCapacidadEstante(
                $estanteNuevo,
                (int) ($data['cantidad'] ?? $inventory->cantidad),
                (int) $inventory->id
            );
            if ($msgCapacidad) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['ok' => false, 'message' => $msgCapacidad], 422);
                }

                return redirect()->back()->with('error', $msgCapacidad);
            }
        }

        $data['user_id'] = Auth::id();

        $data = InventoryService::resolverRelaciones($data);

        $inventory->update($data);

        $cambioUbicacion = ($inventory->pasillo ?? '') !== ($pasilloAnterior ?? '')
            || ($inventory->estante ?? '') !== ($estanteAnterior ?? '')
            || ($inventory->nivel ?? '') !== ($nivelAnterior ?? '')
            || ($inventory->posicion ?? '') !== ($posicionAnterior ?? '');
        $cambioEstado = $estadoAnterior !== $inventory->estado;

        $ubicTxt = [];
        if (!empty($inventory->pasillo) && $inventory->pasillo !== 'P.A') $ubicTxt[] = "Pasillo {$inventory->pasillo}";
        if (!empty($inventory->estante) && $inventory->estante !== 'P.A') $ubicTxt[] = "Estante {$inventory->estante}";
        if (!empty($inventory->nivel)) $ubicTxt[] = "Nivel {$inventory->nivel}";
        if (!empty($inventory->posicion)) $ubicTxt[] = "Posición {$inventory->posicion}";
        $detUbic = !empty($ubicTxt) ? implode(' · ', $ubicTxt) : 'Sin asignar (P.A)';

        $ubicAntTxt = [];
        if (!empty($pasilloAnterior) && $pasilloAnterior !== 'P.A') $ubicAntTxt[] = "Pasillo {$pasilloAnterior}";
        if (!empty($estanteAnterior) && $estanteAnterior !== 'P.A') $ubicAntTxt[] = "Estante {$estanteAnterior}";
        if (!empty($nivelAnterior)) $ubicAntTxt[] = "Nivel {$nivelAnterior}";
        if (!empty($posicionAnterior)) $ubicAntTxt[] = "Posición {$posicionAnterior}";
        $detUbicAnterior = !empty($ubicAntTxt) ? implode(' · ', $ubicAntTxt) : 'Sin asignar (P.A)';

        if ($cambioEstado) {
            StockMovement::create([
                'inventory_id' => $inventory->id,
                'user_id' => Auth::id(),
                'producto' => $inventory->producto,
                'lote' => $inventory->lote,
                'fecha_vencimiento' => $inventory->fecha_vencimiento,
                'tipo_movimiento' => 'Cambio de estado',
                'cantidad' => $inventory->cantidad,
                'fecha_hora' => now(),
                'origen_destino' => 'Desde: ' . $estadoAnterior . ' / Hacia: ' . $inventory->estado,
                'documento_respaldo' => $request->filled('motivo') ? $request->input('motivo') : 'Gestión de inventario',
                'estado_lote' => $inventory->estado,
            ]);
        } elseif ($cambioUbicacion) {
            StockMovement::create([
                'inventory_id' => $inventory->id,
                'user_id' => Auth::id(),
                'producto' => $inventory->producto,
                'lote' => $inventory->lote,
                'fecha_vencimiento' => $inventory->fecha_vencimiento,
                'tipo_movimiento' => 'Reubicación',
                'cantidad' => $inventory->cantidad,
                'fecha_hora' => now(),
                'origen_destino' => 'Ubicación asignada: ' . $detUbic,
                'documento_respaldo' => 'Ordenamiento en bodega',
                'estado_lote' => $inventory->estado,
            ]);
        }

        if ($estadoAnterior === 'Merma' && $inventory->estado === 'Cuarentena') {
            $tipo = 'merma_a_cuarentena';
            $mensaje = "El lote {$inventory->lote} ha sido trasladado desde Merma a Cuarentena para reevaluación técnica.";
        } elseif ($estadoAnterior === 'Merma' && $inventory->estado === 'Disponible') {
            $tipo = 'merma_a_disponible';
            $mensaje = "El lote {$inventory->lote} ha sido reactivado desde Merma a Disponible para entrega y despacho.";
        } elseif ($estadoAnterior === 'Disponible' && $inventory->estado === 'Cuarentena') {
            $tipo = 'disponible_a_cuarentena';
            $mensaje = "El lote {$inventory->lote} ha sido bloqueado preventivamente y enviado a Cuarentena.";
        } elseif ($estadoAnterior === 'Cuarentena' && $inventory->estado === 'Disponible') {
            $tipo = 'cuarentena_a_disponible';
            $mensaje = "El lote {$inventory->lote} ha sido liberado exitosamente como Disponible para despacho.";
        } elseif ($cambioEstado && $inventory->estado === 'Merma') {
            $tipo = 'a_merma';
            $mensaje = "El lote {$inventory->lote} ha sido declarado como Merma y retirado del stock disponible.";
        } elseif ($cambioUbicacion && !$cambioEstado) {
            $tipo = 'reubicacion';
            $mensaje = "El lote {$inventory->lote} ha sido reubicado exitosamente en bodega ({$detUbic}).";
        } elseif (!$cambioEstado && $inventory->estado === 'Cuarentena') {
            $tipo = 'actualizacion_cuarentena';
            $mensaje = "Se ha actualizado la justificación del lote {$inventory->lote} en Cuarentena.";
        } elseif (!$cambioEstado && $inventory->estado === 'Merma') {
            $tipo = 'actualizacion_merma';
            $mensaje = "Se ha actualizado la información del lote {$inventory->lote} en Merma.";
        } else {
            $tipo = 'actualizacion';
            $mensaje = "El lote {$inventory->lote} ha sido actualizado exitosamente.";
        }

        $statusPayload = [
            'tipo' => $tipo,
            'correlativo' => 'MOV-' . str_pad($inventory->id, 5, '0', STR_PAD_LEFT),
            'producto' => $inventory->producto,
            'lote' => $inventory->lote,
            'categoria' => $inventory->categoria ?? 'General',
            'programa' => $inventory->programa ?: null,
            'fecha_vencimiento' => $inventory->fecha_vencimiento ? \Carbon\Carbon::parse($inventory->fecha_vencimiento)->format('d/m/Y') : 'N/A',
            'estado_anterior' => $estadoAnterior,
            'nuevo_estado' => $inventory->estado,
            'cantidad' => number_format($inventory->cantidad ?? 0, 0, ',', '.'),
            'valor_unitario' => $inventory->valor_neto_unitario ? number_format((float) $inventory->valor_neto_unitario, 0, ',', '.') : null,
            'valor_total' => $inventory->valor_neto_unitario ? number_format((float) ($inventory->valor_neto_unitario * ($inventory->cantidad ?? 0)), 0, ',', '.') : null,
            'motivo' => $request->input('motivo') ?: '',
            'ubicacion' => $detUbic,
            'ubicacion_anterior' => $detUbicAnterior,
            'cambio_ubicacion' => $cambioUbicacion,
            'fecha_hora' => now()->format('d/m/Y H:i'),
            'usuario' => Auth::user() ? trim(Auth::user()->name . ' ' . (Auth::user()->last_name ?? '')) : 'Sistema',
            'mensaje' => $mensaje,
        ];

        // peticion ajax: responde json para mostrar el aviso de exito sin recargar la pagina
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $mensaje,
                'data' => $statusPayload,
            ]);
        }

        return redirect()->back()
            ->with('success', $mensaje)
            ->with('lote_status_success', $statusPayload);
    }

    // procesa el archivo excel para carga masiva con validacion de formato, duplicados y transaccion atomica
    public function import(Request $request)
    {
        if (! $request->hasFile('excel')) {
            return redirect()->back()
                ->with('open_modal', 'import')
                ->with('import_error_title', 'Archivo No Seleccionado')
                ->with('error', 'Debe adjuntar un archivo Excel (.xlsx, .xls) o CSV (.csv) antes de iniciar la carga masiva.');
        }

        $file = $request->file('excel');

        if (! $file->isValid()) {
            return redirect()->back()
                ->with('open_modal', 'import')
                ->with('import_error_title', 'Archivo Dañado o Excede el Límite')
                ->with('error', 'El archivo no pudo subirse correctamente. Verifique que no supere los 20 MB y no esté dañado.');
        }

        // la extension se valida aparte porque el navegador puede mandar un mime generico
        $ext = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['xlsx', 'xls', 'csv'];

        if (! in_array($ext, $allowedExtensions, true)) {
            $extMostrar = $ext ?: 'desconocido';
            return redirect()->back()
                ->with('open_modal', 'import')
                ->with('import_error_title', "Formato de Archivo No Compatible (.{$extMostrar})")
                ->with('error', "El archivo «{$file->getClientOriginalName()}» posee un formato inválido (.{$extMostrar}). El sistema solo acepta planillas Excel (.xlsx, .xls) o archivos .csv con la estructura de la Plantilla Oficial.");
        }

        $validator = Validator::make($request->all(), [
            'excel' => 'required|file|max:20480',
        ], [
            'excel.max' => 'El archivo supera el tamaño máximo permitido de 20 MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('open_modal', 'import')
                ->with('import_error_title', 'Error de Validación del Archivo')
                ->with('error', $validator->errors()->first('excel'));
        }

        // se guarda el hash del archivo para avisar si suben dos veces el mismo excel si el inventario quedo vacio, el historial de hashes ya no sirve
        if (Inventory::count() === 0) {
            session()->forget('inventory_imported_hashes');
        }

        $fileHash = hash_file('sha256', $file->getRealPath());
        $importedHashes = session('inventory_imported_hashes', []);

        if (isset($importedHashes[$fileHash]) && ! $request->boolean('force_reimport')) {
            $prev = $importedHashes[$fileHash];
            return redirect()->back()
                ->with('open_modal', 'import')
                ->with('import_error_title', 'Archivo Duplicado Detectado')
                ->with('error', "El archivo «{$file->getClientOriginalName()}» ya fue procesado anteriormente el {$prev['fecha']} ({$prev['registros']} registros importados). Marque la casilla de confirmación si desea volver a procesarlo.");
        }

        try {
            $resumen = DB::transaction(function () use ($file, $request) {
                $import = new InventoriesImport((int) Auth::id(), $request->boolean('force_reimport'));
                Excel::import($import, $file);

                if ($import->importados() === 0) {
                    $omitidasCero = $import->omitidas();
                    if (! empty($omitidasCero)) {
                        throw new \RuntimeException('No se cargó ningún lote. ' . self::resumenFilasOmitidas($omitidasCero));
                    }
                    throw new \RuntimeException('El archivo seleccionado está vacío o no contiene filas con nombre de PRODUCTO bajo la fila de encabezados.');
                }

                return [
                    'total' => $import->importados(),
                    'disponibles' => $import->disponibles(),
                    'cuarentena' => $import->enCuarentena(),
                    'merma' => $import->enMerma(),
                    'omitidas' => $import->omitidas(),
                    'vencidos' => $import->vencidosAMerma(),
                    'ubicaciones' => $import->ubicacionesInvalidas(),
                ];
            });

            $total = $resumen['total'];

            $importedHashes[$fileHash] = [
                'nombre' => $file->getClientOriginalName(),
                'fecha' => date('d/m/Y H:i'),
                'registros' => $total,
            ];
            session(['inventory_imported_hashes' => $importedHashes]);

            $detallesEstado = [];
            if ($resumen['disponibles'] > 0) {
                $detallesEstado[] = "{$resumen['disponibles']} en Inventario Disponible";
            }
            if ($resumen['cuarentena'] > 0) {
                $detallesEstado[] = "{$resumen['cuarentena']} en Cuarentena";
            }
            if ($resumen['merma'] > 0) {
                $detallesEstado[] = "{$resumen['merma']} en Merma";
            }
            $textoDesglose = ! empty($detallesEstado) ? ' (' . implode(', ', $detallesEstado) . ')' : '';

            $mensaje = "Carga masiva completada: {$total} lote(s) ingresados a bodega{$textoDesglose}.";
            $avisos = [];
            if ($resumen['vencidos'] > 0) {
                $avisos[] = "{$resumen['vencidos']} lote(s) venían vencidos y se cargaron directamente como Merma.";
            }
            if (! empty($resumen['ubicaciones'])) {
                $filasUb = implode(', ', array_slice($resumen['ubicaciones'], 0, 15)) . (count($resumen['ubicaciones']) > 15 ? '…' : '');
                $avisos[] = count($resumen['ubicaciones']) . " lote(s) traían una ubicación que no existe en el mapa de bodega y quedaron por asignar (filas {$filasUb}).";
            }
            if (! empty($resumen['omitidas'])) {
                $avisos[] = self::resumenFilasOmitidas($resumen['omitidas']);
            }

            if (! empty($avisos)) {
                return redirect()->back()
                    ->with('import_completed_ok', true)
                    ->with('warning', $mensaje . ' ' . implode(' ', $avisos));
            }

            return redirect()->back()
                ->with('import_completed_ok', true)
                ->with('success', $mensaje);
        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->with('open_modal', 'import')
                ->with('import_error_title', 'Error en Validación de Datos')
                ->with('error', $e->getMessage());
        } catch (ValidationException $e) {
            $primerError = collect($e->failures())->first();
            $fila = $primerError ? $primerError->row() + 1 : '?';
            $mensaje = $primerError ? $primerError->errors()[0] : 'Archivo inválido.';

            return redirect()->back()
                ->with('open_modal', 'import')
                ->with('import_error_title', "Error en Fila {$fila}")
                ->with('error', "Fila {$fila}: {$mensaje}");
        } catch (\Throwable $e) {
            \Log::error('Error en carga masiva de inventario: ' . $e->getMessage());

            return redirect()->back()
                ->with('open_modal', 'import')
                ->with('import_error_title', 'Error al Procesar el Archivo')
                ->with('error', 'No se pudo completar la carga masiva: ' . $e->getMessage());
        }
    }

    // descarga la plantilla oficial de carga masiva con estado disponible por defecto
    public function template()
    {
        $export = new class implements FromArray, WithHeadings, ShouldAutoSize, WithStyles {
            public function headings(): array
            {
                return [
                    'producto',
                    'categoria',
                    'estado',
                    'lote',
                    'fecha_vencimiento',
                    'cantidad',
                    'valor_neto_unitario',
                    'fecha_recepcion',
                    'tipo_documento',
                    'numero_documento',
                    'orden_de_compra',
                    'origen_mercaderia',
                    'rut_proveedor',
                    'razon_social',
                    'pasillo',
                    'estante',
                    'nivel',
                    'posicion',
                    'programa',
                ];
            }

            public function array(): array
            {
                return [
                    [
                        'PARACETAMOL 500MG COMPRIMIDOS',
                        'Medicamentos',
                        'Disponible',
                        'LOT-2026-A1',
                        '31/12/2027',
                        1500,
                        45,
                        date('d/m/Y'),
                        'Factura',
                        '104590',
                        'OC-2026-881',
                        'CENABAST',
                        '61.608.700-2',
                        'CENTRAL DE ABASTECIMIENTO S.N.S.S.',
                        'Pasillo 1',
                        'A-1',
                        '1',
                        'P1',
                        'Fondo de Farmacia (FOFAR)',
                    ],
                ];
            }

            public function styles(Worksheet $sheet): array
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                        'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF0F766E']],
                    ],
                ];
            }
        };

        return Excel::download($export, 'Plantilla_Carga_Masiva_Inventario_DAS.xlsx');
    }

    // arma un texto corto con las filas omitidas de la carga masiva, agrupadas por motivo
    private static function resumenFilasOmitidas(array $omitidas): string
    {
        $porMotivo = [];
        foreach ($omitidas as $fila => $motivo) {
            $clave = preg_replace('/«[^»]*»/u', '', $motivo);
            $porMotivo[$clave]['motivo'] = $motivo;
            $porMotivo[$clave]['filas'][] = $fila;
        }

        $partes = [];
        foreach ($porMotivo as $grupo) {
            $filas = $grupo['filas'];
            $listado = implode(', ', array_slice($filas, 0, 10)) . (count($filas) > 10 ? '…' : '');
            $motivoTexto = count($filas) > 1 ? preg_replace('/«[^»]*»\s*/u', '', $grupo['motivo']) : $grupo['motivo'];
            $partes[] = count($filas) . " fila(s) por {$motivoTexto} (filas {$listado})";
        }

        return count($omitidas) . ' fila(s) no se cargaron: ' . implode('; ', $partes) . '.';
    }
}
