<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TraceabilityController extends Controller
{
    // Función central blindada con DB::table (Sin usar relaciones de Eloquent)
    private function getAllMovements()
    {
        $movements = collect();

        // 1. Extraer Recepciones Históricas
        try {
            $receptions = DB::table('receptions')->get();
            foreach ($receptions as $rec) {
                $items = DB::table('reception_items')->where('reception_id', $rec->id)->get();
                
                // Buscar nombre del proveedor
                $providerName = 'Proveedor Externo';
                if (!empty($rec->provider_id)) {
                    $prov = DB::table('providers')->where('id', $rec->provider_id)->first();
                    if ($prov) {
                        $providerName = $prov->name ?? $prov->nombre ?? $prov->razon_social ?? 'Proveedor Externo';
                    }
                }

                foreach ($items as $item) {
                    // Buscar nombre del producto
                    $productName = 'Producto ID: ' . ($item->product_id ?? 'Desconocido');
                    if (!empty($item->product_id)) {
                        $prod = DB::table('products')->where('id', $item->product_id)->first();
                        if ($prod) {
                            $productName = $prod->nombre ?? $productName;
                        }
                    }

                    $movements->push((object)[
                        'producto' => $productName,
                        'lote' => $item->lote ?? 'S/L',
                        'fecha_vencimiento' => $item->fecha_vencimiento ?? null,
                        'tipo_movimiento' => 'Recepción',
                        'cantidad' => $item->cantidad ?? 0,
                        'fecha_hora' => $rec->created_at ?? $rec->fecha_recepcion ?? now(),
                        'origen_destino' => 'Desde: ' . $providerName,
                        'documento_respaldo' => ($rec->tipo_documento ?? 'Doc') . ' N° ' . ($rec->numero_documento ?? 'S/N'),
                        'estado_lote' => 'Vigente'
                    ]);
                }
            }
        } catch (\Exception $e) {}

        // 2. Extraer Despachos Históricos
        try {
            $dispatches = DB::table('dispatches')->get();
            foreach ($dispatches as $disp) {
                $items = DB::table('dispatch_items')->where('dispatch_id', $disp->id)->get();
                foreach ($items as $item) {
                    $movements->push((object)[
                        'producto' => $item->producto ?? 'Desconocido',
                        'lote' => $item->lote ?? 'S/L',
                        'fecha_vencimiento' => $item->fecha_vencimiento ?? null,
                        'tipo_movimiento' => 'Despacho',
                        'cantidad' => -abs($item->cantidad ?? 0), // Salida siempre en negativo
                        'fecha_hora' => $disp->created_at ?? $disp->fecha_despacho ?? now(),
                        'origen_destino' => 'Hacia: ' . ($disp->establecimiento_destino ?? 'Destino Desconocido'),
                        'documento_respaldo' => 'Guía N° ' . ($disp->correlativo ?? 'S/N'),
                        'estado_lote' => 'Vigente'
                    ]);
                }
            }
        } catch (\Exception $e) {}

        // Devolvemos todo unificado y ordenado
        return $movements->sortByDesc('fecha_hora')->values();
    }

    public function index(Request $request)
    {
        $movements = $this->getAllMovements();

        // FILTROS AVANZADOS
        if ($request->filled('search')) {
            $search = mb_strtolower($request->search, 'UTF-8');
            $movements = $movements->filter(function($item) use ($search) {
                return str_contains(mb_strtolower($item->lote, 'UTF-8'), $search) ||
                       str_contains(mb_strtolower($item->producto, 'UTF-8'), $search) ||
                       str_contains(mb_strtolower($item->documento_respaldo, 'UTF-8'), $search);
            });
        }

        if ($request->filled('tipo_movimiento')) {
            $movements = $movements->where('tipo_movimiento', $request->tipo_movimiento);
        }

        if ($request->filled('fecha_desde')) {
            $movements = $movements->filter(function($item) use ($request) {
                return Carbon::parse($item->fecha_hora)->startOfDay() >= Carbon::parse($request->fecha_desde)->startOfDay();
            });
        }
        if ($request->filled('fecha_hasta')) {
            $movements = $movements->filter(function($item) use ($request) {
                return Carbon::parse($item->fecha_hora)->endOfDay() <= Carbon::parse($request->fecha_hasta)->endOfDay();
            });
        }

        // EXPORTAR A EXCEL (CSV)
        if ($request->has('export') && $request->export == 'csv') {
            return $this->exportCsv($movements);
        }

        // PAGINACIÓN NATIVA
        $perPage = 15;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $currentItems = $movements->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedMovements = new LengthAwarePaginator($currentItems, count($movements), $perPage, $currentPage, [
            'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
            'query' => $request->query()
        ]);

        // KPIs DINÁMICOS
        $totalMovimientos = $movements->filter(function($item) {
            return Carbon::parse($item->fecha_hora)->month == date('m');
        })->count();

        $lotesActivos = Inventory::where('cantidad', '>', 0)->distinct('lote')->count();
        $alertas = Inventory::where('cantidad', '>', 0)
                            ->where('fecha_vencimiento', '<=', Carbon::now()->addMonths(6))
                            ->count();

        return view('traceability.index', [
            'movements' => $paginatedMovements,
            'totalMovimientos' => $totalMovimientos,
            'lotesActivos' => $lotesActivos,
            'alertas' => $alertas
        ]);
    }

    // Endpoint Seguro para el Modal
    public function getTimeline(Request $request)
    {
        try {
            $lote = $request->query('lote');

            if(!$lote) {
                return response()->json([]);
            }

            $timeline = $this->getAllMovements()
                ->where('lote', $lote)
                ->sortBy('fecha_hora') 
                ->map(function ($mov) {
                    return [
                        'tipo' => $mov->tipo_movimiento,
                        'origen_destino' => $mov->origen_destino,
                        'documento' => $mov->documento_respaldo,
                        'fecha' => Carbon::parse($mov->fecha_hora)->format('d/m/Y - H:i'),
                        'cantidad' => $mov->cantidad
                    ];
                })->values();

            return response()->json($timeline);
        } catch (\Exception $e) {
            // Devolvemos el error real para verlo en JS
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Generador de Excel / CSV
    private function exportCsv($movements)
    {
        $filename = "Kardex_Lotes_" . date('Y-m-d_H-i-s') . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Fecha y Hora', 'Producto', 'Lote', 'Vencimiento', 'Movimiento', 'Cantidad', 'Origen / Destino', 'Documento Respaldo'];

        $callback = function() use($movements, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($file, $columns, ';');

            foreach ($movements as $mov) {
                fputcsv($file, [
                    Carbon::parse($mov->fecha_hora)->format('d/m/Y H:i'),
                    $mov->producto,
                    $mov->lote,
                    $mov->fecha_vencimiento ? Carbon::parse($mov->fecha_vencimiento)->format('d/m/Y') : 'N/A',
                    $mov->tipo_movimiento,
                    $mov->cantidad,
                    $mov->origen_destino,
                    $mov->documento_respaldo
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}