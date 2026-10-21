<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TraceabilityController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('lote', 'like', "%{$search}%")
                    ->orWhere('producto', 'like', "%{$search}%")
                    ->orWhere('documento_respaldo', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo_movimiento')) {
            $query->where('tipo_movimiento', $request->input('tipo_movimiento'));
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_hora', '>=', $request->input('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_hora', '<=', $request->input('fecha_hasta'));
        }

        $movements = $query->orderBy('fecha_hora', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        if ($request->has('export') && $request->export == 'excel') {
            return $this->exportExcelStyled($query->orderBy('fecha_hora', 'desc')->get());
        }

        $mesesAlerta = config('drogueria.meses_alerta_vencimiento');

        $totalMovimientos = StockMovement::whereMonth('fecha_hora', now()->month)
            ->whereYear('fecha_hora', now()->year)
            ->count();

        $lotesActivos = Inventory::where('cantidad', '>', 0)->distinct('lote')->count('lote');

        $alertas = Inventory::where('cantidad', '>', 0)
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', Carbon::now()->addMonths($mesesAlerta))
            ->count();

        return view('traceability.index', compact('movements', 'totalMovimientos', 'lotesActivos', 'alertas'));
    }

    public function getTimeline(Request $request)
    {
        $lote = $request->query('lote');
        $producto = $request->query('producto');

        if (! $lote || ! $producto) {
            return response()->json([]);
        }

        // Filtramos por lote y por producto para evitar colisiones
        $timeline = StockMovement::where('lote', $lote)
            ->where('producto', $producto)
            ->orderBy('fecha_hora')
            ->orderBy('id')
            ->get()
            ->map(fn ($mov) => [
                'tipo' => $mov->tipo_movimiento,
                'origen_destino' => $mov->origen_destino,
                'documento' => $mov->documento_respaldo,
                'fecha' => Carbon::parse($mov->fecha_hora)->format('d/m/Y - H:i'),
                'cantidad' => $mov->cantidad,
            ]);

        // Buscamos la ficha exacta del inventario cruzando lote y producto
        $inventory = Inventory::where('lote', $lote)
            ->where('producto', $producto)
            ->first();

        $details = $inventory ? [
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
        ] : null;

        return response()->json([
            'timeline' => $timeline,
            'details' => $details,
        ]);
    }

    private function exportExcelStyled($movements)
    {
        $filename = 'Reporte_Trazabilidad_Lotes_' . date('Y-m-d_H-i') . '.xls';

        $html = '<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
        $html .= '<table border="1" style="font-family: Arial, sans-serif; border-collapse: collapse;">';
        $html .= '<thead>';
        $html .= '<tr style="background-color: #0f172a; color: #ffffff; text-align: center; height: 30px;">';
        $html .= '<th>FECHA Y HORA</th><th>PRODUCTO</th><th>LOTE</th><th>VENCIMIENTO</th><th>TIPO MOVIMIENTO</th><th>CANTIDAD</th><th>ORIGEN / DESTINO</th><th>DOCUMENTO RESPALDO</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($movements as $mov) {
            $colorMovimiento = $mov->tipo_movimiento == 'Recepción' ? 'color: #047857; font-weight: bold;' : 'color: #1d4ed8; font-weight: bold;';
            $fechaVence = $mov->fecha_vencimiento ? Carbon::parse($mov->fecha_vencimiento)->format('d/m/Y') : 'N/A';

            $html .= '<tr style="text-align: center;">';
            $html .= '<td>' . Carbon::parse($mov->fecha_hora)->format('d/m/Y H:i') . '</td>';
            $html .= '<td style="text-align: left; font-weight: bold;">' . e($mov->producto) . '</td>';
            $html .= '<td style="mso-number-format:\'@\';">' . e($mov->lote) . '</td>';
            $html .= '<td>' . $fechaVence . '</td>';
            $html .= '<td style="' . $colorMovimiento . '">' . e($mov->tipo_movimiento) . '</td>';
            $html .= '<td style="font-weight: bold;">' . $mov->cantidad . '</td>';
            $html .= '<td style="text-align: left;">' . e($mov->origen_destino) . '</td>';
            $html .= '<td>' . e($mov->documento_respaldo) . '</td>';
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