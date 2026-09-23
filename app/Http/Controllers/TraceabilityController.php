<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TraceabilityController extends Controller
{
    private function getAllMovements()
    {
        $movements = collect();

        // 1. Extraer Recepciones
        try {
            $receptions = DB::table('receptions')->get();
            foreach ($receptions as $rec) {
                $items = DB::table('reception_items')->where('reception_id', $rec->id)->get();
                
                $providerName = 'Proveedor Externo';
                if (!empty($rec->provider_id)) {
                    $prov = DB::table('providers')->where('id', $rec->provider_id)->first();
                    if ($prov) {
                        $providerName = $prov->name ?? $prov->nombre ?? $prov->razon_social ?? 'Proveedor Externo';
                    }
                }

                foreach ($items as $item) {
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

        // 2. Extraer Despachos
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
                        'cantidad' => -abs($item->cantidad ?? 0),
                        'fecha_hora' => $disp->created_at ?? $disp->fecha_despacho ?? now(),
                        'origen_destino' => 'Hacia: ' . ($disp->establecimiento_destino ?? 'Destino Desconocido'),
                        'documento_respaldo' => 'Guía N° ' . ($disp->correlativo ?? 'S/N'),
                        'estado_lote' => 'Vigente'
                    ]);
                }
            }
        } catch (\Exception $e) {}

        return $movements->sortByDesc('fecha_hora')->values();
    }

    public function index(Request $request)
    {
        $movements = $this->getAllMovements();

        // FILTROS
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

        // EXPORTACIÓN A EXCEL CON ESTILOS VISUALES
        if ($request->has('export') && $request->export == 'excel') {
            return $this->exportExcelStyled($movements);
        }

        $perPage = 15;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $currentItems = $movements->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedMovements = new LengthAwarePaginator($currentItems, count($movements), $perPage, $currentPage, [
            'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
            'query' => $request->query()
        ]);

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

    public function getTimeline(Request $request)
    {
        try {
            $lote = $request->query('lote');

            if(!$lote) {
                return response()->json([]);
            }

            // 1. Línea de tiempo
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

            // 2. Ficha Completa desde Inventory (Datos de Recepción Original)
            $inventory = Inventory::where('lote', $lote)->first();
            $details = null;
            
            if ($inventory) {
                $details = [
                    'producto' => $inventory->producto ?? 'S/N',
                    'categoria' => $inventory->categoria ?? 'General',
                    'proveedor' => $inventory->razon_social ?? 'S/R',
                    'rut_proveedor' => $inventory->rut_proveedor ?? 'S/R',
                    'documento' => ($inventory->tipo_documento ?? 'Documento') . ' N° ' . ($inventory->numero_documento ?? 'S/N'),
                    'orden_compra' => $inventory->orden_compra ?? 'S/OC',
                    'fecha_recepcion' => $inventory->fecha_recepcion ? Carbon::parse($inventory->fecha_recepcion)->format('d/m/Y') : 'S/R',
                    'valor_unitario' => '$' . number_format($inventory->valor_neto_unitario ?? 0, 0, ',', '.'),
                    'estado_actual' => $inventory->estado ?? 'Vigente',
                    'stock_actual' => $inventory->cantidad ?? 0,
                    'ubicacion' => 'Pasillo ' . ($inventory->pasillo ?? 'N/A') . ' - Estante ' . ($inventory->estante ?? 'N/A'),
                ];
            }

            return response()->json([
                'timeline' => $timeline,
                'details' => $details
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // EXPORTACIÓN EXCEL NATIVA (Soporta colores y negritas sin librerías extras)
    private function exportExcelStyled($movements)
    {
        $filename = "Reporte_Trazabilidad_Lotes_" . date('Y-m-d_H-i') . ".xls";
        
        $html = '<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
        $html .= '<table border="1" style="font-family: Arial, sans-serif; border-collapse: collapse;">';
        
        // Cabeceras del Excel con colores corporativos
        $html .= '<thead>';
        $html .= '<tr style="background-color: #0f172a; color: #ffffff; text-align: center; height: 30px;">';
        $html .= '<th>FECHA Y HORA</th>';
        $html .= '<th>PRODUCTO</th>';
        $html .= '<th>LOTE</th>';
        $html .= '<th>VENCIMIENTO</th>';
        $html .= '<th>TIPO MOVIMIENTO</th>';
        $html .= '<th>CANTIDAD</th>';
        $html .= '<th>ORIGEN / DESTINO</th>';
        $html .= '<th>DOCUMENTO RESPALDO</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        
        $html .= '<tbody>';
        foreach ($movements as $mov) {
            $colorMovimiento = $mov->tipo_movimiento == 'Recepción' ? 'color: #047857; font-weight: bold;' : 'color: #1d4ed8; font-weight: bold;';
            $fechaVence = $mov->fecha_vencimiento ? Carbon::parse($mov->fecha_vencimiento)->format('d/m/Y') : 'N/A';
            
            $html .= '<tr style="text-align: center;">';
            $html .= '<td>' . Carbon::parse($mov->fecha_hora)->format('d/m/Y H:i') . '</td>';
            $html .= '<td style="text-align: left; font-weight: bold;">' . $mov->producto . '</td>';
            $html .= '<td style="mso-number-format:\'@\';">' . $mov->lote . '</td>';
            $html .= '<td>' . $fechaVence . '</td>';
            $html .= '<td style="' . $colorMovimiento . '">' . $mov->tipo_movimiento . '</td>';
            $html .= '<td style="font-weight: bold;">' . $mov->cantidad . '</td>';
            $html .= '<td style="text-align: left;">' . $mov->origen_destino . '</td>';
            $html .= '<td>' . $mov->documento_respaldo . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}