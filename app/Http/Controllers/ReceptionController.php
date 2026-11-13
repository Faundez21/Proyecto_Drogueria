<?php

namespace App\Http\Controllers;

use App\Models\Aisle;
use App\Models\Inventory;
use App\Models\Level;
use App\Models\Position;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Reception;
use App\Models\ReceptionItem;
use App\Models\Shelf;
use App\Services\InventoryService;
use App\Support\Rut;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ReceptionController extends Controller
{
    public function index()
    {
        foreach (InventoryService::controladosOficiales() as $ctrlNombre) {
            Product::firstOrCreate(['nombre' => $ctrlNombre]);
        }

        // Los productos que entraron por carga masiva o ingreso directo también deben aparecer acá
        $productosEnInventario = Inventory::whereNotNull('producto')
            ->where('producto', '!=', '')
            ->distinct()
            ->pluck('producto');

        foreach ($productosEnInventario as $prodInvNombre) {
            Product::firstOrCreate(['nombre' => trim($prodInvNombre)]);
        }

        $providers = Provider::where('status', 'activo')->orderBy('name')->get();
        $products = Product::orderBy('nombre')->get();

        // Categoría y programa sugeridos para cada producto, sacados de lo que ya está cargado
        $categoriasPorNombre = [];
        $categoriasPorId = [];
        $programasPorNombre = [];
        $programasPorId = [];

        $itemsRecHist = ReceptionItem::with('product:id,nombre')
            ->whereNotNull('tipo_producto')
            ->where('tipo_producto', '!=', '')
            ->orderBy('id', 'asc')
            ->get(['product_id', 'tipo_producto']);

        foreach ($itemsRecHist as $itemHist) {
            $catVal = trim((string) $itemHist->tipo_producto);
            if ($catVal === '' || mb_strtolower($catVal) === 'general') {
                continue;
            }
            if ($itemHist->product_id) {
                $categoriasPorId[(int) $itemHist->product_id] = $catVal;
            }
            if ($itemHist->product?->nombre) {
                $categoriasPorNombre[mb_strtolower(trim($itemHist->product->nombre))] = $catVal;
            }
        }

        $registrosInv = Inventory::select('product_id', 'producto', 'categoria', 'programa')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($registrosInv as $regInv) {
            $keyNombre = mb_strtolower(trim((string) $regInv->producto));
            $prodId = $regInv->product_id ? (int) $regInv->product_id : null;

            $catVal = trim((string) ($regInv->categoria ?? ''));
            if ($catVal !== '') {
                $esGeneral = mb_strtolower($catVal) === 'general';
                if ($keyNombre !== '' && (! $esGeneral || ! isset($categoriasPorNombre[$keyNombre]))) {
                    $categoriasPorNombre[$keyNombre] = $catVal;
                }
                if ($prodId && (! $esGeneral || ! isset($categoriasPorId[$prodId]))) {
                    $categoriasPorId[$prodId] = $catVal;
                }
            }

            $progVal = trim((string) ($regInv->programa ?? ''));
            if ($progVal !== '') {
                if ($keyNombre !== '') {
                    $programasPorNombre[$keyNombre] = $progVal;
                }
                if ($prodId) {
                    $programasPorId[$prodId] = $progVal;
                }
            }
        }

        foreach ($products as $prod) {
            $keyProd = mb_strtolower(trim($prod->nombre));
            $catHist = $categoriasPorId[(int) $prod->id] ?? ($categoriasPorNombre[$keyProd] ?? null);
            $progHist = $programasPorId[(int) $prod->id] ?? ($programasPorNombre[$keyProd] ?? '');

            $prod->grupo_recepcion = InventoryService::clasificarGrupoProducto($prod->nombre, $catHist);
            $prod->programa_sugerido = $progHist;
            $prod->categoria_sugerida = ($catHist && mb_strtolower($catHist) !== 'general')
                ? $catHist
                : ($prod->grupo_recepcion === 'Estupefacientes' ? 'Estupefacientes' : ($prod->grupo_recepcion === 'Insumos' ? 'Insumos' : 'Medicamentos'));
        }

        // Categorías existentes en Inventario y Carga Masiva, agrupadas por clasificación.
        // No hay listas fijas en código: todo sale de los datos cargados.
        $categoriasAgrupadas = [
            'Medicamentos' => [],
            'Insumos' => [],
            'Estupefacientes' => [],
        ];

        $categoriasEnBD = Inventory::whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->distinct()
            ->pluck('categoria')
            ->merge(
                ReceptionItem::whereNotNull('tipo_producto')
                    ->where('tipo_producto', '!=', '')
                    ->distinct()
                    ->pluck('tipo_producto')
            )
            ->map(fn ($c) => trim((string) $c))
            ->filter(fn ($c) => $c !== '' && mb_strtolower($c) !== 'general')
            ->unique();

        foreach ($categoriasEnBD as $catBD) {
            $yaExiste = false;
            foreach ($categoriasAgrupadas as $grp => $lista) {
                foreach ($lista as $valKey => $labelText) {
                    if (mb_strtolower($valKey) === mb_strtolower($catBD)) {
                        $yaExiste = true;
                        break 2;
                    }
                }
            }
            if (! $yaExiste) {
                $grupoDestino = InventoryService::clasificarGrupoProducto('', $catBD);
                $categoriasAgrupadas[$grupoDestino][$catBD] = mb_strtoupper($catBD);
            }
        }

        $categoriasAgrupadas = array_filter($categoriasAgrupadas, fn ($lista) => ! empty($lista));

        // Programas existentes en Inventario y Carga Masiva (sin listas fijas en código)
        $programasLista = Inventory::whereNotNull('programa')
            ->where('programa', '!=', '')
            ->distinct()
            ->pluck('programa')
            ->map(fn ($p) => trim((string) $p))
            ->filter()
            ->unique()
            ->sort(SORT_NATURAL)
            ->values();

        $productosAgrupados = [
            'Medicamentos' => $products->where('grupo_recepcion', 'Medicamentos')->values(),
            'Insumos' => $products->where('grupo_recepcion', 'Insumos')->values(),
            'Estupefacientes' => $products->where('grupo_recepcion', 'Estupefacientes')->values(),
        ];

        $receptions = Reception::with(['provider', 'items.product'])->orderBy('created_at', 'desc')->get();

        // Asociar el programa registrado en inventario a cada ítem de recepción para el historial
        $invPorRecepcion = Inventory::whereNotNull('reception_id')
            ->whereNotNull('programa')
            ->where('programa', '!=', '')
            ->get(['reception_id', 'product_id', 'lote', 'programa']);

        foreach ($receptions as $rec) {
            foreach ($rec->items as $rItem) {
                $match = $invPorRecepcion->first(function ($inv) use ($rec, $rItem) {
                    return (int) $inv->reception_id === (int) $rec->id
                        && (int) $inv->product_id === (int) $rItem->product_id;
                });
                $nomProd = mb_strtolower(trim($rItem->product?->nombre ?? ''));
                $rItem->setAttribute('programa', $match?->programa ?? ($programasPorId[(int) $rItem->product_id] ?? ($programasPorNombre[$nomProd] ?? null)));
            }
        }
        $nextCorrelativo = (int) Reception::max('correlativo') + 1;
        $controlados = InventoryService::controlados();

        $datosDist = InventoryService::datosDistribucionFisica();
        $pasillosLista = $datosDist['pasillosLista'];
        $estantesLista = $datosDist['estantesLista'];
        $nivelesLista = $datosDist['nivelesLista'];
        $posicionesLista = $datosDist['posicionesLista'];
        $distribucionJerarquia = $datosDist['distribucionJerarquia'];
        $ubicacionesOcupadas = $datosDist['ubicacionesOcupadas'];

        // datos minimos para el formulario de edicion del historial
        $recepcionesEditables = $receptions->mapWithKeys(fn ($rec) => [$rec->id => [
            'id' => $rec->id,
            'correlativo' => $rec->correlativo,
            'fecha_recepcion' => $rec->fecha_recepcion ? date('Y-m-d', strtotime($rec->fecha_recepcion)) : null,
            'tipo_documento' => $rec->tipo_documento,
            'numero_documento' => $rec->numero_documento,
            'orden_compra' => $rec->orden_compra,
            'origen' => $rec->origen,
            'provider_id' => $rec->provider_id,
            'proveedor' => $rec->provider->name ?? null,
            'items' => $rec->items->map(fn ($it) => [
                'id' => $it->id,
                'producto' => $it->product->nombre ?? ('Producto #' . $it->product_id),
                'lote' => in_array($it->lote, [null, '', 'S/L'], true) ? '' : $it->lote,
                'fecha_vencimiento' => $it->fecha_vencimiento ? date('Y-m-d', strtotime($it->fecha_vencimiento)) : null,
                'cantidad' => (int) $it->cantidad,
                'valor_unitario' => (int) round((float) $it->valor_unitario),
            ])->values(),
        ]]);

        return view('reception.index', compact(
            'recepcionesEditables',
            'providers',
            'receptions',
            'products',
            'productosAgrupados',
            'categoriasAgrupadas',
            'programasLista',
            'nextCorrelativo',
            'controlados',
            'pasillosLista',
            'estantesLista',
            'nivelesLista',
            'posicionesLista',
            'distribucionJerarquia',
            'ubicacionesOcupadas'
        ));
    }

    public function store(Request $request)
    {
        // Los campos vacíos llegan como cadena vacía y hacen fallar las reglas de fecha, por eso se pasan a null
        if ($request->has('fecha_vencimiento') && is_array($request->fecha_vencimiento)) {
            $fechas = array_map(function ($val) {
                if (is_string($val)) {
                    $val = trim($val);
                    return $val === '' ? null : $val;
                }
                return $val ?: null;
            }, $request->fecha_vencimiento);
            $request->merge(['fecha_vencimiento' => $fechas]);
        }

        if ($request->has('orden_compra') && empty(trim((string)$request->orden_compra))) {
            $request->merge(['orden_compra' => null]);
        }

        $validated = $request->validate([
            'fecha_recepcion' => ['required', 'date', 'before_or_equal:today'],
            'tipo_documento' => ['required', 'string', 'max:255'],
            'numero_documento' => ['required', 'string', 'max:255'],
            'orden_compra' => ['nullable', 'string', 'max:255'],
            'origen' => ['required', 'string', 'max:255'],
            'provider_id' => ['required', function ($attr, $val, $fail) {
                // __nuevo__ = proveedor que se registra en esta misma recepcion
                if ($val !== '__nuevo__' && ! Provider::where('id', $val)->where('status', 'activo')->exists()) {
                    $fail('El proveedor seleccionado no existe o está inactivo.');
                }
            }],
            'proveedor_nuevo' => ['nullable', 'array'],
            'proveedor_nuevo.rut' => ['required_if:provider_id,__nuevo__', 'nullable', 'string', 'max:20'],
            'proveedor_nuevo.nombre' => ['required_if:provider_id,__nuevo__', 'nullable', 'string', 'max:255'],
            'proveedor_nuevo.categoria' => ['required_if:provider_id,__nuevo__', 'nullable', Rule::in(ProviderController::CATEGORIAS)],
            'proveedor_nuevo.correo' => ['required_if:provider_id,__nuevo__', 'nullable', 'email', 'max:255'],
            'firma_receptor' => ['required', 'string'],
            'action' => ['nullable', 'string', 'in:save,save_only,save_and_download'],
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', function ($attr, $val, $fail) {
                // __nuevo__ = producto que se crea en esta misma recepcion
                if ($val !== '__nuevo__' && (! ctype_digit((string) $val) || ! Product::whereKey($val)->exists())) {
                    $fail('Uno de los productos seleccionados no existe.');
                }
            }],
            'producto_nuevo' => ['nullable', 'array'],
            'producto_nuevo.*' => ['nullable', 'string', 'max:1000'],
            'estado_ingreso' => ['nullable', 'array'],
            'estado_ingreso.*' => ['nullable', 'in:Disponible,Cuarentena,Merma'],
            'motivo_ingreso' => ['nullable', 'array'],
            'motivo_ingreso.*' => ['nullable', 'string', 'max:1000'],
            'cantidad_unitaria' => ['required', 'array', 'min:1'],
            'cantidad_unitaria.*' => ['required', 'integer', 'min:1'],
            'valor_unitario' => ['nullable', 'array'],
            'valor_unitario.*' => ['nullable', 'numeric', 'min:0'],
            'lote' => ['nullable', 'array'],
            'lote.*' => ['nullable', 'string', 'max:255'],
            'fecha_vencimiento' => ['required', 'array', 'min:1'],
            'fecha_vencimiento.*' => ['required', 'date'],
            'tipo_producto' => ['nullable', 'array'],
            'tipo_producto.*' => ['nullable', 'string', 'max:500'],
            'programa' => ['nullable', 'array'],
            'programa.*' => ['nullable', 'string', 'max:500'],
            'pasillo' => ['nullable', 'array'],
            'pasillo.*' => ['nullable', 'string', 'max:50'],
            'estante' => ['nullable', 'array'],
            'estante.*' => ['nullable', 'string', 'max:50'],
            'nivel' => ['nullable', 'array'],
            'nivel.*' => ['nullable', 'string', 'max:100'],
            'posicion' => ['nullable', 'array'],
            'posicion.*' => ['nullable', 'string', 'max:100'],
        ], [
            'fecha_vencimiento.required' => 'Debe ingresar la fecha de vencimiento para cada producto.',
            'fecha_vencimiento.*.required' => 'La fecha de vencimiento es obligatoria para cada producto recepcionado.',
            'fecha_vencimiento.*.date' => 'La fecha de vencimiento ingresada no es válida.',
            'fecha_vencimiento.*.after_or_equal' => 'Uno de los productos ya está vencido. No se puede recepcionar un lote vencido.',
            'fecha_recepcion.before_or_equal' => 'La fecha de recepción no puede ser posterior a hoy.',
            'firma_receptor.required' => 'Debe firmar el acta de recepción antes de confirmarla.',
            'proveedor_nuevo.*.required_if' => 'Completa RUT, razón social, categoría y correo del proveedor nuevo.',
            'proveedor_nuevo.correo.email' => 'El correo del proveedor nuevo no es válido.',
        ]);

        $responderError = function (string $mensaje) use ($request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'message' => $mensaje], 422);
            }

            return redirect()->back()->withInput()->with('error', $mensaje);
        };

        // estado, motivo y nombre de cada fila (el producto nuevo usa el nombre escrito)
        $estadoFila = fn ($i) => $validated['estado_ingreso'][$i] ?? 'Disponible';
        $nombresNuevos = [];
        foreach ($validated['product_id'] as $index => $prodId) {
            $fila = $index + 1;
            if ($prodId === '__nuevo__') {
                $nombre = trim(preg_replace('/\s+/', ' ', (string) ($validated['producto_nuevo'][$index] ?? '')));
                if ($nombre === '') {
                    return $responderError("Escribe el nombre del producto nuevo (fila {$fila}).");
                }
                // si ya existe con ese nombre se usa el mismo producto
                $existente = Product::whereRaw('LOWER(TRIM(nombre)) = ?', [mb_strtolower($nombre)])->value('id');
                if ($existente) {
                    $validated['product_id'][$index] = (string) $existente;
                } else {
                    $nombresNuevos[$index] = $nombre;
                }
            }
            if ($estadoFila($index) !== 'Disponible' && trim((string) ($validated['motivo_ingreso'][$index] ?? '')) === '') {
                return $responderError("Indica el motivo para ingresar la fila {$fila} como {$estadoFila($index)}.");
            }
            $vence = $validated['fecha_vencimiento'][$index] ?? null;
            if ($vence && strtotime($vence) < strtotime(date('Y-m-d')) && $estadoFila($index) !== 'Merma') {
                return $responderError("El producto de la fila {$fila} ya está vencido. Un lote vencido solo puede ingresar como Merma.");
            }
        }
        $nombreFila = fn ($i) => $nombresNuevos[$i] ?? (Product::find($validated['product_id'][$i])?->nombre ?? ('Producto #' . $validated['product_id'][$i]));

        // proveedor nuevo: mismas reglas que en el modulo de proveedores
        $proveedorNuevo = null;
        if ($validated['provider_id'] === '__nuevo__') {
            $rutNuevo = (string) ($validated['proveedor_nuevo']['rut'] ?? '');
            if (! Rut::esValido($rutNuevo)) {
                return $responderError('El RUT del proveedor nuevo no es válido. Revisa el número y el dígito verificador.');
            }
            $rutNuevo = Rut::formatear($rutNuevo);
            if (Provider::where('rut', $rutNuevo)->exists()) {
                return $responderError("Ya existe un proveedor con el RUT {$rutNuevo}. Selecciónalo de la lista.");
            }
            $proveedorNuevo = [
                'rut' => $rutNuevo,
                'name' => trim($validated['proveedor_nuevo']['nombre']),
                'category' => $validated['proveedor_nuevo']['categoria'],
                'email' => trim($validated['proveedor_nuevo']['correo']),
                'status' => 'activo',
            ];
        }

        // Evitar ingresar dos veces el mismo documento (misma factura o guía del mismo proveedor)
        $documentoDuplicado = $proveedorNuevo ? null : Reception::where('provider_id', $validated['provider_id'])
            ->whereRaw('LOWER(TRIM(tipo_documento)) = ?', [mb_strtolower(trim($validated['tipo_documento']))])
            ->whereRaw('LOWER(TRIM(numero_documento)) = ?', [mb_strtolower(trim($validated['numero_documento']))])
            ->first();

        if ($documentoDuplicado) {
            $msgError = "La {$validated['tipo_documento']} N° {$validated['numero_documento']} de este proveedor ya fue recepcionada (Acta N° {$documentoDuplicado->correlativo}). No se puede ingresar dos veces el mismo documento.";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'message' => $msgError], 422);
            }

            return redirect()->back()->withInput()->with('error', $msgError);
        }

        // Validar que cada ubicación indicada exista en el mapa de bodega
        foreach ($validated['product_id'] as $index => $prodId) {
            if ($estadoFila($index) === 'Merma') {
                continue;
            }
            $errorUbicacion = InventoryService::validarUbicacion(
                $validated['pasillo'][$index] ?? null,
                $validated['estante'][$index] ?? null,
                $validated['nivel'][$index] ?? null,
                $validated['posicion'][$index] ?? null
            );

            if ($errorUbicacion) {
                $nomProd = $nombreFila($index);
                $msgError = "Ubicación inválida para «{$nomProd}»: {$errorUbicacion}";
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['ok' => false, 'message' => $msgError], 422);
                }

                return redirect()->back()->withInput()->with('error', $msgError);
            }
        }

        // Dos lotes no pueden quedar en la misma casilla, ni siquiera dentro de la misma acta
        $ubicacionesEnActa = [];
        $unidadesPorEstante = [];
        foreach ($validated['product_id'] as $index => $prodId) {
            $estCheck = ! empty($validated['estante'][$index]) ? trim((string) $validated['estante'][$index]) : '';
            $nivCheck = ! empty($validated['nivel'][$index]) ? InventoryService::normalizarNivel($validated['nivel'][$index]) : '';
            $posCheck = ! empty($validated['posicion'][$index]) ? InventoryService::normalizarPosicion($validated['posicion'][$index]) : '';

            if ($estCheck === '' || strtoupper($estCheck) === 'P.A' || $nivCheck === '' || $estadoFila($index) === 'Merma') {
                continue;
            }

            $nomProdCheck = $nombreFila($index);
            $loteCheck = ! empty($validated['lote'][$index]) ? trim((string) $validated['lote'][$index]) : 'S/L';

            $ocupanteBD = InventoryService::verificarUbicacionOcupada(
                $estCheck,
                $nivCheck,
                $posCheck,
                null,
                $nomProdCheck,
                $loteCheck
            );

            if ($ocupanteBD) {
                $detallePos = $posCheck !== '' ? ", Posición {$posCheck}" : '';
                $msgError = "La ubicación seleccionada para «{$nomProdCheck}» (Estante {$estCheck}, Nivel {$nivCheck}{$detallePos}) ya está ocupada en bodega por «{$ocupanteBD->producto}» (Lote: " . ($ocupanteBD->lote ?: 'S/L') . ").";
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['ok' => false, 'message' => $msgError], 422);
                }

                return redirect()->back()->withInput()->with('error', $msgError);
            }

            // Capacidad del estante: se suman las unidades de todas las filas que van al mismo estante
            $estKeyCap = mb_strtolower($estCheck);
            $unidadesPorEstante[$estKeyCap] = ($unidadesPorEstante[$estKeyCap] ?? 0) + (int) ($validated['cantidad_unitaria'][$index] ?? 0);
            $msgCapacidad = InventoryService::verificarCapacidadEstante($estCheck, $unidadesPorEstante[$estKeyCap]);
            if ($msgCapacidad) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['ok' => false, 'message' => $msgCapacidad], 422);
                }

                return redirect()->back()->withInput()->with('error', $msgCapacidad);
            }

            $slotKey = mb_strtolower($estCheck) . '|' . mb_strtolower($nivCheck) . '|' . mb_strtolower($posCheck);
            $prodLoteKey = mb_strtolower(trim($nomProdCheck)) . '|' . mb_strtolower((string) InventoryService::normalizarLote($loteCheck));
            if ($posCheck !== '' && isset($ubicacionesEnActa[$slotKey]) && $ubicacionesEnActa[$slotKey]['prod_lote'] !== $prodLoteKey) {
                $otro = $ubicacionesEnActa[$slotKey]['nombre'];
                $msgError = "No puede asignar la misma ubicación (Estante {$estCheck}, Nivel {$nivCheck}, Posición {$posCheck}) a dos lotes distintos en la misma recepción («{$otro}» y «{$nomProdCheck}»).";
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['ok' => false, 'message' => $msgError], 422);
                }

                return redirect()->back()->withInput()->with('error', $msgError);
            }
            $ubicacionesEnActa[$slotKey] = ['prod_lote' => $prodLoteKey, 'nombre' => $nomProdCheck];
        }

        DB::beginTransaction();

        try {
            $correlativo = (int) Reception::lockForUpdate()->max('correlativo') + 1;

            // se crean aqui para que se reviertan si la recepcion falla
            $provider = $proveedorNuevo ? Provider::create($proveedorNuevo) : Provider::findOrFail($validated['provider_id']);
            $nombreProveedor = $provider->name ?? $provider->nombre ?? $provider->razon_social ?? 'Proveedor';
            $idsProductos = $validated['product_id'];
            foreach ($nombresNuevos as $index => $nombre) {
                $idsProductos[$index] = Product::create(['nombre' => $nombre])->id;
            }

            $reception = Reception::create([
                'correlativo' => $correlativo,
                'fecha_recepcion' => $validated['fecha_recepcion'],
                'tipo_documento' => $validated['tipo_documento'],
                'numero_documento' => $validated['numero_documento'],
                'orden_compra' => $validated['orden_compra'] ?? null,
                'origen' => $validated['origen'],
                'provider_id' => $provider->id,
                'valor_total_recepcion' => 0,
                'firma_receptor' => $validated['firma_receptor'] ?? null,
            ]);

            $ivaConfig = (float) config('drogueria.iva', 0.19);
            $factorIva = 1 + ($ivaConfig > 1 ? ($ivaConfig / 100) : $ivaConfig);
            $total_neto_recepcion = 0;
            $itemsResumen = [];

            foreach ($validated['product_id'] as $index => $prodIdOriginal) {
                $prodId = $idsProductos[$index];
                $estadoIngreso = $estadoFila($index);
                $esMerma = $estadoIngreso === 'Merma';
                $product = Product::find($prodId);
                $nombreProducto = $product?->nombre ?? 'Producto #' . $prodId;

                $cantidad = (int) $validated['cantidad_unitaria'][$index];
                $valorU = (float) ($validated['valor_unitario'][$index] ?? 0);
                $subtotal = $cantidad * $valorU;
                $subtotalIva = round($subtotal * $factorIva);
                $rawVence = $validated['fecha_vencimiento'][$index] ?? null;
                $fechaVence = (!empty($rawVence) && trim((string)$rawVence) !== '') ? trim((string)$rawVence) : null;
                $loteItem = !empty($validated['lote'][$index]) ? trim($validated['lote'][$index]) : 'S/L';

                $tipoSolicitado = $validated['tipo_producto'][$index] ?? null;
                $grupoProducto = InventoryService::clasificarGrupoProducto($nombreProducto, $tipoSolicitado);

                $categoriaRecepcion = $grupoProducto === 'Estupefacientes'
                    ? 'Estupefacientes'
                    : ($tipoSolicitado ?: $grupoProducto);

                // El inventario guarda la misma categoría que la recepción:
                // la que eligió el usuario o, si no eligió, su grupo de clasificación.
                $categoriaInventario = $categoriaRecepcion;

                $total_neto_recepcion += $subtotal;

                ReceptionItem::create([
                    'reception_id' => $reception->id,
                    'product_id' => $prodId,
                    'tipo_producto' => $categoriaRecepcion,
                    'lote' => $loteItem,
                    'fecha_vencimiento' => $fechaVence,
                    'cantidad' => $cantidad,
                    'valor_unitario' => $valorU,
                    'valor_total_item' => $subtotalIva,
                ]);

                $batch = InventoryService::ingresar([
                    'user_id' => Auth::id(),
                    'product_id' => $prodId,
                    'provider_id' => $provider->id,
                    'reception_id' => $reception->id,
                    'correlativo_interno' => $correlativo,
                    'fecha_recepcion' => $validated['fecha_recepcion'],
                    'tipo_documento' => $validated['tipo_documento'],
                    'numero_documento' => $validated['numero_documento'],
                    'orden_compra' => $validated['orden_compra'] ?? null,
                    'origen_mercaderia' => $validated['origen'],
                    'rut_proveedor' => $provider->rut ?? null,
                    'razon_social' => $nombreProveedor,
                    'producto' => $nombreProducto,
                    'categoria' => $categoriaInventario,
                    'estado' => $estadoIngreso,
                    'motivo' => $estadoIngreso !== 'Disponible' ? trim((string) ($validated['motivo_ingreso'][$index] ?? '')) : null,
                    'lote' => $loteItem,
                    'fecha_vencimiento' => $fechaVence,
                    'cantidad' => $cantidad,
                    'valor_neto_unitario' => $valorU,
                    'programa' => $validated['programa'][$index] ?? null,
                    'pasillo' => (! $esMerma && !empty($validated['pasillo'][$index])) ? trim($validated['pasillo'][$index]) : 'P.A',
                    'estante' => (! $esMerma && !empty($validated['estante'][$index])) ? trim($validated['estante'][$index]) : 'P.A',
                    'nivel' => (! $esMerma && !empty($validated['nivel'][$index])) ? trim($validated['nivel'][$index]) : null,
                    'posicion' => (! $esMerma && !empty($validated['posicion'][$index])) ? trim($validated['posicion'][$index]) : null,
                ]);

                InventoryService::registrarMovimiento($batch, [
                    'user_id' => Auth::id(),
                    'tipo_movimiento' => 'Recepción',
                    'cantidad' => $cantidad,
                    'fecha_hora' => now(),
                    'origen_destino' => 'Desde: ' . $nombreProveedor,
                    'documento_respaldo' => $validated['tipo_documento'] . ' ' . $validated['numero_documento'],
                    'estado_lote' => $estadoIngreso,
                ]);

                $pasVal = !empty($validated['pasillo'][$index]) ? trim($validated['pasillo'][$index]) : '';
                $estVal = !empty($validated['estante'][$index]) ? trim($validated['estante'][$index]) : '';
                $nivVal = !empty($validated['nivel'][$index]) ? trim($validated['nivel'][$index]) : '';

                $ubicacionTexto = 'P.A';
                if ($estVal && $estVal !== 'P.A') {
                    $ubicacionTexto = ($pasVal && $pasVal !== 'P.A') ? "{$pasVal} / {$estVal}" : $estVal;
                    if ($nivVal) $ubicacionTexto .= " (N: {$nivVal})";
                } elseif ($pasVal && $pasVal !== 'P.A') {
                    $ubicacionTexto = $pasVal;
                }

                $itemsResumen[] = [
                    'producto' => $nombreProducto,
                    'lote' => $loteItem,
                    'vencimiento' => !empty($fechaVence) ? date('d/m/Y', strtotime($fechaVence)) : 'N/A',
                    'cantidad' => (int) $cantidad,
                    'tipo' => $categoriaInventario,
                    'programa' => !empty($validated['programa'][$index]) ? trim($validated['programa'][$index]) : ($batch->programa ?: 'Sin programa'),
                    'subtotal_iva' => $subtotalIva,
                    'ubicacion' => $ubicacionTexto,
                ];
            }

            $valorTotalConIva = round($total_neto_recepcion * $factorIva);
            $reception->update(['valor_total_recepcion' => $valorTotalConIva]);

            DB::commit();

            $receptionDetail = [
                'correlativo' => $correlativo,
                'fecha_recepcion' => date('d/m/Y', strtotime($validated['fecha_recepcion'])),
                'origen' => $validated['origen'],
                'orden_compra' => !empty($validated['orden_compra']) ? $validated['orden_compra'] : 'N/A',
                'proveedor' => $nombreProveedor,
                'rut_proveedor' => $provider->rut ?? 'N/A',
                'documento' => $validated['tipo_documento'] . ' N° ' . $validated['numero_documento'],
                'total_items' => count($itemsResumen),
                'total_unidades' => array_sum(array_column($itemsResumen, 'cantidad')),
                'valor_total' => $valorTotalConIva,
                'items' => $itemsResumen,
                'pdf_url' => route('reception.pdf', $reception->id),
                'auto_download' => ($validated['action'] ?? '') === 'save_and_download',
            ];

            if ($request->expectsJson() or $request->ajax()) {
                return response()->json([
                    'ok' => true,
                    'message' => "Recepción guardada y registrada en trazabilidad. N°: {$correlativo}",
                    'detail' => $receptionDetail,
                ]);
            }

            return redirect()
                ->route('reception.index')
                ->with('success', "Recepción guardada y registrada en trazabilidad. N°: {$correlativo}")
                ->with('reception_success_detail', $receptionDetail);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al guardar recepción: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if ($request->expectsJson() or $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Ocurrió un error al guardar la recepción: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Ocurrió un error al guardar la recepción. Intente nuevamente.')
                ->withInput();
        }
    }

    // edita una recepcion ya registrada y deja cada cambio en trazabilidad
    public function update(Request $request, Reception $reception)
    {
        $validated = $request->validate([
            'fecha_recepcion' => ['required', 'date', 'before_or_equal:today'],
            'tipo_documento' => ['required', 'string', 'max:255'],
            'numero_documento' => ['required', 'string', 'max:255'],
            'orden_compra' => ['nullable', 'string', 'max:255'],
            'origen' => ['required', 'string', 'max:255'],
            'provider_id' => ['required', 'integer', 'exists:providers,id'],
            'motivo' => ['required', 'string', 'max:300'],
            'items' => ['required', 'array'],
            'items.*.lote' => ['nullable', 'string', 'max:255'],
            'items.*.fecha_vencimiento' => ['required', 'date'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'items.*.valor_unitario' => ['nullable', 'numeric', 'min:0'],
        ], [
            'motivo.required' => 'Indica el motivo de la edición.',
            'items.*.cantidad.min' => 'La cantidad de cada producto debe ser al menos 1.',
        ]);

        $error = fn (string $m) => response()->json(['ok' => false, 'message' => $m], 422);

        $duplicado = Reception::where('id', '!=', $reception->id)
            ->where('provider_id', $validated['provider_id'])
            ->whereRaw('LOWER(TRIM(tipo_documento)) = ?', [mb_strtolower(trim($validated['tipo_documento']))])
            ->whereRaw('LOWER(TRIM(numero_documento)) = ?', [mb_strtolower(trim($validated['numero_documento']))])
            ->first();
        if ($duplicado) {
            return $error("La {$validated['tipo_documento']} N° {$validated['numero_documento']} de ese proveedor ya está en el Acta N° {$duplicado->correlativo}.");
        }

        $fmtFecha = fn ($f) => $f ? date('d/m/Y', strtotime($f)) : 'sin fecha';
        $provider = Provider::findOrFail($validated['provider_id']);
        $nombreProveedor = $provider->name ?? 'Proveedor';
        $acta = "Acta N° {$reception->correlativo}";
        $motivo = trim($validated['motivo']);

        DB::beginTransaction();
        try {
            $reception = Reception::lockForUpdate()->findOrFail($reception->id);
            $reception->load(['items.product', 'provider']);

            // cambios del documento
            $cambiosDoc = [];
            $campos = [
                'fecha_recepcion' => 'Fecha',
                'tipo_documento' => 'Tipo de documento',
                'numero_documento' => 'N° documento',
                'orden_compra' => 'Orden de compra',
                'origen' => 'Origen',
            ];
            foreach ($campos as $campo => $etiqueta) {
                $antes = (string) ($campo === 'fecha_recepcion' ? date('Y-m-d', strtotime($reception->$campo)) : ($reception->$campo ?? ''));
                $despues = (string) ($validated[$campo] ?? '');
                if (trim($antes) !== trim($despues)) {
                    $cambiosDoc[] = $campo === 'fecha_recepcion'
                        ? "{$etiqueta}: {$fmtFecha($antes)} → {$fmtFecha($despues)}"
                        : "{$etiqueta}: " . ($antes ?: '—') . " → " . ($despues ?: '—');
                }
            }
            if ((int) $reception->provider_id !== (int) $provider->id) {
                $cambiosDoc[] = 'Proveedor: ' . ($reception->provider->name ?? '—') . " → {$nombreProveedor}";
            }

            $ivaConfig = (float) config('drogueria.iva', 0.19);
            $factorIva = 1 + ($ivaConfig > 1 ? ($ivaConfig / 100) : $ivaConfig);
            $documentoNuevo = $validated['tipo_documento'] . ' ' . $validated['numero_documento'];
            $movimientos = [];
            $lotesDeLaRecepcion = [];
            $totalNeto = 0;

            foreach ($reception->items as $item) {
                $datos = $validated['items'][$item->id] ?? null;
                if (! $datos) {
                    $totalNeto += (float) $item->cantidad * (float) $item->valor_unitario;
                    continue;
                }

                $nombre = $item->product->nombre ?? ('Producto #' . $item->product_id);
                $loteAntes = InventoryService::normalizarLote($item->lote);
                $loteNuevo = InventoryService::normalizarLote($datos['lote'] ?? null);
                $venceAntes = $item->fecha_vencimiento ? date('Y-m-d', strtotime($item->fecha_vencimiento)) : null;
                $venceNuevo = date('Y-m-d', strtotime($datos['fecha_vencimiento']));
                $cantAntes = (int) $item->cantidad;
                $cantNueva = (int) $datos['cantidad'];
                $valorAntes = (float) $item->valor_unitario;
                $valorNuevo = (float) ($datos['valor_unitario'] ?? 0);

                // lote en bodega que recibio estas unidades (puede compartirse con otras recepciones)
                $batch = Inventory::where('product_id', $item->product_id)
                    ->where(function ($q) use ($loteAntes) {
                        $loteAntes === null
                            ? $q->whereNull('lote')->orWhere('lote', 'S/L')->orWhere('lote', '')
                            : $q->where('lote', $loteAntes);
                    })
                    ->orderByRaw('CASE WHEN reception_id = ? THEN 0 ELSE 1 END', [$reception->id])
                    ->orderByDesc('cantidad')
                    ->lockForUpdate()
                    ->first();
                if (! $batch) {
                    throw new \RuntimeException("No se encontró en bodega el lote " . ($loteAntes ?? 'S/L') . " de «{$nombre}».");
                }

                $cambios = [];
                $delta = $cantNueva - $cantAntes;
                if ($delta !== 0) {
                    if ((int) $batch->cantidad + $delta < 0) {
                        throw new \RuntimeException("En bodega solo quedan {$batch->cantidad} unidades del lote " . ($loteAntes ?? 'S/L') . " de «{$nombre}»; no se puede bajar la cantidad recibida de {$cantAntes} a {$cantNueva}.");
                    }
                    $batch->cantidad = (int) $batch->cantidad + $delta;
                    $cambios[] = "Cantidad: {$cantAntes} → {$cantNueva}";
                }
                if ($valorAntes != $valorNuevo) {
                    $cambios[] = 'Valor unitario: $' . number_format($valorAntes, 0, ',', '.') . ' → $' . number_format($valorNuevo, 0, ',', '.');
                }

                $cambiaLote = $loteAntes !== $loteNuevo;
                $cambiaVence = $venceAntes !== $venceNuevo;
                $destino = $batch;

                if ($cambiaLote || $cambiaVence) {
                    if ($cambiaLote) {
                        $cambios[] = 'Lote: ' . ($loteAntes ?? 'S/L') . ' → ' . ($loteNuevo ?? 'S/L');
                    }
                    if ($cambiaVence) {
                        $cambios[] = "Vencimiento: {$fmtFecha($venceAntes)} → {$fmtFecha($venceNuevo)}";
                    }

                    if ((int) $batch->cantidad === $cantNueva) {
                        // el lote tiene solo las unidades de esta recepcion: se corrige directo
                        $batch->lote = $loteNuevo ?? 'S/L';
                        $batch->fecha_vencimiento = $venceNuevo;
                    } elseif ((int) $batch->cantidad > $cantNueva) {
                        // el lote esta mezclado con otras recepciones: se separan solo estas unidades
                        $batch->cantidad = (int) $batch->cantidad - $cantNueva;
                        $batch->save();
                        $copia = $batch->replicate();
                        $copia->fill([
                            'lote' => $loteNuevo ?? 'S/L',
                            'fecha_vencimiento' => $venceNuevo,
                            'cantidad' => $cantNueva,
                            'reception_id' => $reception->id,
                        ]);
                        $copia->save();
                        $destino = $copia;
                    } else {
                        throw new \RuntimeException("Del lote " . ($loteAntes ?? 'S/L') . " de «{$nombre}» ya se despacharon unidades; no se puede cambiar su lote o vencimiento.");
                    }
                }

                if ($valorAntes != $valorNuevo && (int) $destino->cantidad === $cantNueva) {
                    $destino->valor_neto_unitario = $valorNuevo;
                }
                $batch->save();
                if ($destino !== $batch) {
                    $destino->save();
                }

                $item->update([
                    'lote' => $loteNuevo ?? 'S/L',
                    'fecha_vencimiento' => $venceNuevo,
                    'cantidad' => $cantNueva,
                    'valor_unitario' => $valorNuevo,
                    'valor_total_item' => round($cantNueva * $valorNuevo * $factorIva),
                ]);
                $totalNeto += $cantNueva * $valorNuevo;
                $lotesDeLaRecepcion[$destino->id] = $destino;

                if ($cambios) {
                    $movimientos[] = [$destino, $destino !== $batch ? $cantNueva : $delta, $cambios];
                    if ($destino !== $batch) {
                        // el lote original entrega las unidades al lote corregido
                        $movimientos[] = [$batch, -$cantNueva, ['Unidades trasladadas al lote ' . ($loteNuevo ?? 'S/L')]];
                    }
                }
            }

            // los datos del documento se copian a los lotes de esta recepcion
            Inventory::where('reception_id', $reception->id)->update([
                'fecha_recepcion' => $validated['fecha_recepcion'],
                'tipo_documento' => $validated['tipo_documento'],
                'numero_documento' => $validated['numero_documento'],
                'orden_compra' => $validated['orden_compra'] ?? null,
                'origen_mercaderia' => $validated['origen'],
                'provider_id' => $provider->id,
                'rut_proveedor' => $provider->rut,
                'razon_social' => $nombreProveedor,
            ]);

            $reception->update([
                'fecha_recepcion' => $validated['fecha_recepcion'],
                'tipo_documento' => $validated['tipo_documento'],
                'numero_documento' => $validated['numero_documento'],
                'orden_compra' => $validated['orden_compra'] ?? null,
                'origen' => $validated['origen'],
                'provider_id' => $provider->id,
                'valor_total_recepcion' => round($totalNeto * $factorIva),
            ]);

            // si solo cambio el documento, igual queda registrado en cada lote de la recepcion
            if ($cambiosDoc && ! $movimientos) {
                foreach (Inventory::where('reception_id', $reception->id)->get() as $lote) {
                    $movimientos[] = [$lote, 0, []];
                }
            }
            if (! $cambiosDoc && ! $movimientos) {
                DB::rollBack();
                return $error('No hiciste ningún cambio en la recepción.');
            }

            foreach ($movimientos as [$lote, $cantidad, $cambios]) {
                $detalle = implode(' · ', array_merge($cambios, $cambiosDoc));
                InventoryService::registrarMovimiento($lote, [
                    'user_id' => Auth::id(),
                    'tipo_movimiento' => 'Edición de recepción',
                    'cantidad' => $cantidad,
                    'fecha_hora' => now(),
                    'origen_destino' => mb_substr("{$acta} · {$detalle} · Motivo: {$motivo}", 0, 500),
                    'documento_respaldo' => $documentoNuevo,
                    'estado_lote' => $lote->estado,
                ]);
            }

            DB::commit();
        } catch (\RuntimeException $e) {
            DB::rollBack();
            return $error($e->getMessage());
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al editar recepción: ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Ocurrió un error al guardar la edición.'], 500);
        }

        return response()->json(['ok' => true, 'message' => "{$acta} actualizada. Los cambios quedaron registrados en Trazabilidad."]);
    }

    public function downloadPdf(Reception $reception)
    {
        $reception->load(['provider', 'items.product']);
        $pdf = Pdf::loadView('reception.pdf', compact('reception'));

        return $pdf->download("recepcion_{$reception->numero_documento}.pdf");
    }
}