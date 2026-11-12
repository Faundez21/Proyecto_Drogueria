<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TraceabilityController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with('user');

        $user = auth()->user();
        $puedeVerControlados = $user && ($user->hasRole('Administrador') || $user->can('ver.despacho_controlado'));
        $controladosLista = InventoryService::controlados();

        if (! $puedeVerControlados) {
            $query->whereNotIn('producto', $controladosLista)
                ->where('tipo_movimiento', '!=', 'Despacho Controlado');
        }

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

        if ($request->has('export') && $request->export == 'excel') {
            return $this->exportExcelStyled((clone $query)->orderBy('fecha_hora', 'desc')->get());
        }

        $movements = $query->orderBy('fecha_hora', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $mesesAlerta = config('drogueria.meses_alerta_vencimiento');

        $totalMovimientos = StockMovement::whereMonth('fecha_hora', now()->month)
            ->whereYear('fecha_hora', now()->year)
            ->when(! $puedeVerControlados, fn ($q) => $q->whereNotIn('producto', $controladosLista))
            ->count();

        $lotesActivos = Inventory::where('cantidad', '>', 0)
            ->when(! $puedeVerControlados, fn ($q) => $q->where('categoria', '!=', 'Fármacos Controlados'))
            ->distinct('lote')
            ->count('lote');

        $alertas = Inventory::where('cantidad', '>', 0)
            ->when(! $puedeVerControlados, fn ($q) => $q->where('categoria', '!=', 'Fármacos Controlados'))
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

        $user = auth()->user();
        $puedeVerControlados = $user && ($user->hasRole('Administrador') || $user->can('ver.despacho_controlado'));
        if (! $puedeVerControlados && InventoryService::esControlado($producto)) {
            return response()->json(['timeline' => [], 'details' => null]);
        }

        // Hay lotes con el mismo número en productos distintos, por eso se filtra por ambos
        $timeline = StockMovement::with('user')
            ->where('lote', $lote)
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
                'responsable' => $mov->user ? trim($mov->user->name . ' ' . ($mov->user->last_name ?? '')) : null,
            ]);

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

        $totalMovimientos = count($movements);
        $totalUnidadesMovidas = collect($movements)->sum('cantidad');
        $totalRecepciones = collect($movements)->where('tipo_movimiento', 'Recepción')->count();
        $totalDespachos = collect($movements)->whereIn('tipo_movimiento', ['Despacho', 'Despacho Controlado'])->count();

        $html = '<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
        $html .= '<table border="1" style="font-family: Calibri, Arial, sans-serif; border-collapse: collapse; border-color: #cbd5e1;">';

        // Encabezado del Excel
        $html .= '<tr>';
        $html .= '<td colspan="8" style="background-color: #0f766e; color: #ffffff; font-size: 16px; font-weight: bold; padding: 10px; text-align: left;">';
        $html .= 'DROGUERÍA COMUNAL DAS - I. MUNICIPALIDAD DE TALCAHUANO';
        $html .= '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td colspan="8" style="background-color: #f0fdfa; color: #134e4a; font-size: 12px; font-weight: bold; padding: 6px 10px; text-align: left;">';
        $html .= 'AUDITORÍA Y TRAZABILIDAD HISTÓRICA DE LOTES · Emitido el ' . date('d/m/Y H:i') . ' hrs';
        $html .= '</td>';
        $html .= '</tr>';

        // Fila de totales del encabezado
        $html .= '<tr style="background-color: #f8fafc; font-size: 11px; text-align: center; height: 26px;">';
        $html .= '<td colspan="2" style="padding: 6px;"><strong>Total Eventos:</strong> ' . number_format($totalMovimientos, 0, ',', '.') . '</td>';
        $html .= '<td colspan="2" style="padding: 6px; color: #047857;"><strong>Recepciones:</strong> ' . number_format($totalRecepciones, 0, ',', '.') . '</td>';
        $html .= '<td colspan="2" style="padding: 6px; color: #1d4ed8;"><strong>Despachos:</strong> ' . number_format($totalDespachos, 0, ',', '.') . '</td>';
        $html .= '<td colspan="2" style="padding: 6px; color: #0f766e;"><strong>Unidades Movilizadas:</strong> ' . number_format($totalUnidadesMovidas, 0, ',', '.') . '</td>';
        $html .= '</tr>';

        $html .= '<thead>';
        $html .= '<tr style="background-color: #0f172a; color: #ffffff; text-align: center; height: 32px; font-size: 11px; font-weight: bold;">';
        $html .= '<th style="padding: 8px;">FECHA Y HORA</th>';
        $html .= '<th style="padding: 8px;">PRODUCTO / DESCRIPCIÓN</th>';
        $html .= '<th style="padding: 8px;">N° DE LOTE</th>';
        $html .= '<th style="padding: 8px;">VENCIMIENTO</th>';
        $html .= '<th style="padding: 8px;">TIPO DE MOVIMIENTO</th>';
        $html .= '<th style="padding: 8px;">CANTIDAD</th>';
        $html .= '<th style="padding: 8px;">ORIGEN / ESTABLECIMIENTO DESTINO</th>';
        $html .= '<th style="padding: 8px;">DOCUMENTO DE RESPALDO</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($movements as $idx => $mov) {
            $bgRow = ($idx % 2 === 1) ? 'background-color: #f8fafc;' : 'background-color: #ffffff;';
            if ($mov->tipo_movimiento === 'Recepción') {
                $colorMovimiento = 'color: #047857; background-color: #ecfdf5; font-weight: bold;';
            } elseif ($mov->tipo_movimiento === 'Despacho Controlado') {
                $colorMovimiento = 'color: #be123c; background-color: #fff1f2; font-weight: bold;';
            } elseif ($mov->tipo_movimiento === 'Despacho') {
                $colorMovimiento = 'color: #1d4ed8; background-color: #eff6ff; font-weight: bold;';
            } else {
                $colorMovimiento = 'color: #b45309; background-color: #fffbeb; font-weight: bold;';
            }

            $fechaVence = $mov->fecha_vencimiento ? Carbon::parse($mov->fecha_vencimiento)->format('d/m/Y') : 'N/A';

            $html .= '<tr style="text-align: center; font-size: 11px; ' . $bgRow . '">';
            $html .= '<td style="padding: 6px;">' . Carbon::parse($mov->fecha_hora)->format('d/m/Y H:i') . '</td>';
            $html .= '<td style="padding: 6px; text-align: left; font-weight: bold; color: #0f172a;">' . e($mov->producto) . '</td>';
            $html .= '<td style="padding: 6px; mso-number-format:\'@\'; font-family: Consolas, monospace; font-weight: bold;">' . e($mov->lote) . '</td>';
            $html .= '<td style="padding: 6px;">' . $fechaVence . '</td>';
            $html .= '<td style="padding: 6px; ' . $colorMovimiento . '">' . e($mov->tipo_movimiento) . '</td>';
            $html .= '<td style="padding: 6px; font-weight: bold; mso-number-format:\'#,##0\';">' . (int) $mov->cantidad . '</td>';
            $html .= '<td style="padding: 6px; text-align: left;">' . e($mov->origen_destino) . '</td>';
            $html .= '<td style="padding: 6px; font-weight: bold; color: #334155;">' . e($mov->documento_respaldo) . '</td>';
            $html .= '</tr>';
        }

        // Fila de totales al final
        $html .= '<tr style="background-color: #ccfbf1; color: #134e4a; font-weight: bold; font-size: 11px; height: 28px;">';
        $html .= '<td colspan="5" style="text-align: right; padding: 6px 10px;">TOTAL UNIDADES MOVILIZADAS EN EL PERÍODO:</td>';
        $html .= '<td style="text-align: center; padding: 6px; mso-number-format:\'#,##0\';">' . (int) $totalUnidadesMovidas . '</td>';
        $html .= '<td colspan="2" style="padding: 6px;"></td>';
        $html .= '</tr>';

        $html .= '</tbody></table>';

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}