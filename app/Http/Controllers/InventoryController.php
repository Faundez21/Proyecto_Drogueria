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
    /**
     * Muestra la lista de inventario principal con sus filtros y búsqueda.
     */
    public function index(Request $request)
    {
        $query = Inventory::query();

        // El directorio principal SOLO muestra productos Disponibles
        $query->where('estado', 'Disponible');

        // Solo el rol Administrador o quien tenga ver.despacho_controlado puede ver los productos de la categoría "Fármacos Controlados"
        $puedeVerControlados = auth()->user()->hasRole('Administrador') || auth()->user()->can('ver.despacho_controlado');

        if (! $puedeVerControlados) {
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
            $query->latest();
        }

        // 4. Ejecución de la consulta con paginación
        $inventories = $query->paginate(15)->withQueryString();

        // --- CONSULTAS INDEPENDIENTES PARA LOS MODALES ---
        $mermas = Inventory::with('user')
            ->where('estado', 'Merma')
            ->when(! $puedeVerControlados, fn ($q) => $q->where('categoria', '!=', 'Fármacos Controlados'))
            ->orderBy('updated_at', 'desc')
            ->get();
        $historial = Inventory::with('user')
            ->when(! $puedeVerControlados, fn ($q) => $q->where('categoria', '!=', 'Fármacos Controlados'))
            ->orderBy('created_at', 'desc')
            ->limit(150)
            ->get();

        // 5. Generar lista de productos únicos combinando Catálogo Maestro + Inventario
        $productosInv = Inventory::select('producto')
            ->whereNotNull('producto')
            ->where('producto', '!=', '')
            ->distinct()
            ->pluck('producto');
        $productosCat = Product::orderBy('nombre')->pluck('nombre');
        $productosLista = $productosInv->merge($productosCat)->unique()->sort()->values();

        // 6. Listas para filtros y formularios combinando Mantenedores Físicos + Inventario
        $programasLista = Inventory::whereNotNull('programa')
            ->where('programa', '!=', '')
            ->distinct()
            ->orderBy('programa', 'asc')
            ->pluck('programa');

        $pasillosInv = Inventory::whereNotNull('pasillo')->where('pasillo', '!=', '')->distinct()->pluck('pasillo');
        $pasillosMant = Aisle::orderBy('name')->pluck('name');
        $pasillosLista = $pasillosMant->merge($pasillosInv)->unique()->sort(SORT_NATURAL)->values();

        $estantesInv = Inventory::whereNotNull('estante')->where('estante', '!=', '')->distinct()->pluck('estante');
        $estantesMant = Shelf::orderBy('name')->pluck('name');
        $estantesLista = $estantesMant->merge($estantesInv)->unique()->sort(SORT_NATURAL)->values();

        $nivelesInv = Inventory::whereNotNull('nivel')->where('nivel', '!=', '')->distinct()->pluck('nivel')->map(fn ($v) => (string) $v);
        $nivelesMant = Level::orderBy('number')->pluck('number')->map(fn ($v) => (string) $v);
        $nivelesLista = $nivelesMant->merge($nivelesInv)->unique()->sort(SORT_NATURAL)->values();

        $posicionesInv = Inventory::whereNotNull('posicion')->where('posicion', '!=', '')->distinct()->pluck('posicion');
        $posicionesMant = Position::orderBy('name')->pluck('name');
        $posicionesLista = $posicionesMant->merge($posicionesInv)->unique()->sort(SORT_NATURAL)->values();

        return view('inventory.index', compact(
            'inventories',
            'mermas',
            'historial',
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
        $validator = Validator::make($request->all(), [
            'correlativo_interno' => 'nullable|integer|min:1',
            'fecha_recepcion' => 'required|date',
            'tipo_documento' => 'required|string|max:255',
            'numero_documento' => 'required|string|max:255',
            'orden_compra' => 'nullable|string|max:255',
            'origen_mercaderia' => 'nullable|string|max:255',
            'rut_proveedor' => 'nullable|string|max:30',
            'razon_social' => 'nullable|string|max:255',
            'producto' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'estado' => 'required|in:Disponible,Cuarentena,Merma',
            'motivo' => 'nullable|string|max:1000',
            'lote' => 'nullable|string|max:255',
            'fecha_vencimiento' => 'nullable|date',
            'cantidad' => 'required|integer|min:1',
            'valor_neto_unitario' => 'nullable|integer|min:0',
            'pasillo' => 'nullable|string|max:50',
            'estante' => 'nullable|string|max:50',
            'programa' => 'nullable|string|max:255',
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'deposit')
                ->with('error', 'Por favor revise los campos obligatorios en el formulario de Ingreso Manual.');
        }

        $validated = $validator->validated();

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

                // Asegurar que lote y valor_neto_unitario mantengan sus valores por defecto
                $validated['lote'] = $validated['lote'] ?? 'S/L';
                $validated['valor_neto_unitario'] = $validated['valor_neto_unitario'] ?? 0;

                foreach (['solicitud_pedido_mensual', 'solicitud_stock_critico', 'adelanto_de_pedido', 'traspaso'] as $flag) {
                    $validated[$flag] = $request->boolean($flag);
                }

                $validated = InventoryService::resolverRelaciones($validated);

                // Si ya existe exactamente el mismo producto + lote + estado, acumulamos stock o creamos registro limpio
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

    /**
     * Actualiza el estado (Mandar a Cuarentena, Merma o Disponible) desde el Modal.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'estado' => 'required|in:Cuarentena,Disponible,Merma',
            'motivo' => 'nullable|string|max:1000',
        ]);

        $inventory = Inventory::findOrFail($id);
        $estadoAnterior = $inventory->estado;
        $data = $request->only(['estado', 'motivo']);

        $data['user_id'] = Auth::id();

        $inventory->update($data);

        if ($estadoAnterior !== $inventory->estado) {
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
        }

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
     * Procesa el archivo Excel para carga masiva con validación de formato, duplicados y transacción atómica.
     */
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

        // 1. Validar extensión del archivo explícitamente
        $ext = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['xlsx', 'xls', 'csv'];

        if (! in_array($ext, $allowedExtensions, true)) {
            $extMostrar = $ext ?: 'desconocido';
            return redirect()->back()
                ->with('open_modal', 'import')
                ->with('import_error_title', "Formato de Archivo No Compatible (.{$extMostrar})")
                ->with('error', "El archivo «{$file->getClientOriginalName()}» posee un formato inválido (.{$extMostrar}). El sistema solo acepta planillas Excel (.xlsx, .xls) o archivos .csv con la estructura de la Plantilla Oficial.");
        }

        // 2. Validar MIME y tamaño máximo (20 MB)
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

        // 3. Detectar si el archivo exacto ya fue importado (por Hash SHA-256)
        $fileHash = hash_file('sha256', $file->getRealPath());
        $importedHashes = session('inventory_imported_hashes', []);

        if (isset($importedHashes[$fileHash]) && ! $request->boolean('force_reimport')) {
            $prev = $importedHashes[$fileHash];
            return redirect()->back()
                ->with('open_modal', 'import')
                ->with('import_error_title', 'Archivo Duplicado Detectado')
                ->with('error', "El archivo «{$file->getClientOriginalName()}» ya fue procesado anteriormente el {$prev['fecha']} ({$prev['registros']} registros importados). Se bloqueó la subida para evitar duplicar existencias.");
        }

        try {
            $total = DB::transaction(function () use ($file) {
                $import = new InventoriesImport(Auth::id());
                Excel::import($import, $file);

                if ($import->importados() === 0) {
                    throw new \RuntimeException('El archivo seleccionado está vacío o no contiene registros válidos bajo la fila de encabezados.');
                }

                return $import->importados();
            });

            // Guardar huella del archivo importado en la sesión
            $importedHashes[$fileHash] = [
                'nombre' => $file->getClientOriginalName(),
                'fecha' => date('d/m/Y H:i'),
                'registros' => $total,
            ];
            session(['inventory_imported_hashes' => $importedHashes]);

            return redirect()->back()->with('success', "Carga masiva completada con éxito: {$total} lote(s) de inventario verificados e ingresados a bodega.");
        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->with('open_modal', 'import')
                ->with('import_error_title', 'Error en Validación de Datos / Duplicados')
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
                ->with('import_error_title', 'Archivo Incompatible o Corrupto')
                ->with('error', 'No fue posible leer la estructura del archivo. Asegúrese de que sea un archivo Excel (.xlsx / .xls) válido generado desde la Plantilla Oficial.');
        }
    }

    /**
     * Descarga la plantilla oficial de carga masiva (o la genera dinámicamente si no existe en disco).
     */
    public function template()
    {
        $filePath = public_path('plantillas/plantilla_inventario.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'Plantilla_Carga_Masiva_Inventario_DAS.xlsx');
        }

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
                        'Factura Electrónica',
                        '104590',
                        'OC-2026-881',
                        'CENABAST',
                        '61.608.700-K',
                        'CENTRAL DE ABASTECIMIENTO S.N.S.S.',
                        'P1',
                        'E1',
                        '1',
                        'A1',
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
}
