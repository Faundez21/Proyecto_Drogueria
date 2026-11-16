<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // por si un intento anterior quedo a medias (mysql no deshace la creacion de tablas)
        Schema::dropIfExists('establishment_stock_lots');

        // lotes que tiene cada establecimiento de un producto (la parte del saldo sin lote conocido queda como S/L)
        Schema::create('establishment_stock_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_stock_id')->constrained('establishment_stocks')->cascadeOnDelete();
            $table->string('lote');
            $table->date('fecha_vencimiento')->nullable();
            $table->integer('cantidad')->default(0);
            $table->timestamps();
            $table->index(['establishment_stock_id', 'fecha_vencimiento'], 'est_stock_lots_stock_venc_idx');
        });

        // el historial de saldos tambien guarda el lote que entro o salio
        if (! Schema::hasColumn('establishment_stock_movements', 'lote')) {
            Schema::table('establishment_stock_movements', function (Blueprint $table) {
                $table->string('lote')->nullable()->after('saldo');
                $table->date('fecha_vencimiento')->nullable()->after('lote');
            });
        }

        $this->cargarLotesIniciales();
    }

    public function down(): void
    {
        Schema::table('establishment_stock_movements', function (Blueprint $table) {
            $table->dropColumn(['lote', 'fecha_vencimiento']);
        });
        Schema::dropIfExists('establishment_stock_lots');
    }

    // reconstruye los lotes del saldo actual con lo que la drogueria les despacho, del despacho mas reciente al mas antiguo
    private function cargarLotesIniciales(): void
    {
        $ahora = now();
        $saldos = DB::table('establishment_stocks')->where('cantidad', '>', 0)->whereNotNull('product_id')->get();

        foreach ($saldos as $saldo) {
            $est = mb_strtolower(trim($saldo->establecimiento));

            $normales = DB::table('dispatch_items as di')
                ->join('dispatches as d', 'd.id', '=', 'di.dispatch_id')
                ->where('d.clasificacion', 'Externo')
                ->whereRaw('LOWER(TRIM(d.establecimiento_destino)) = ?', [$est])
                ->where('di.product_id', $saldo->product_id)
                ->get(['di.lote', 'di.fecha_vencimiento', 'di.cantidad', 'd.created_at']);

            $controlados = DB::table('controlled_dispatch_items as ci')
                ->join('controlled_dispatches as c', 'c.id', '=', 'ci.controlled_dispatch_id')
                ->whereRaw('LOWER(TRIM(c.establecimiento_destino)) = ?', [$est])
                ->where('ci.product_id', $saldo->product_id)
                ->get(['ci.lote', 'ci.fecha_vencimiento', 'ci.cantidad', 'c.created_at']);

            $recibidos = $normales->merge($controlados)
                ->filter(fn ($r) => ! in_array(mb_strtoupper(trim((string) $r->lote)), ['', 'S/L', 'N/A'], true))
                ->sortByDesc('created_at');

            $restante = (int) $saldo->cantidad;
            $lotes = [];
            foreach ($recibidos as $r) {
                if ($restante <= 0) {
                    break;
                }
                $cant = min($restante, (int) $r->cantidad);
                $venc = $r->fecha_vencimiento ? date('Y-m-d', strtotime($r->fecha_vencimiento)) : null;
                $clave = mb_strtolower(trim($r->lote)) . '|' . $venc;
                $lotes[$clave] ??= ['lote' => trim($r->lote), 'fecha_vencimiento' => $venc, 'cantidad' => 0];
                $lotes[$clave]['cantidad'] += $cant;
                $restante -= $cant;
            }

            foreach ($lotes as $l) {
                DB::table('establishment_stock_lots')->insert([
                    'establishment_stock_id' => $saldo->id,
                    'lote' => $l['lote'],
                    'fecha_vencimiento' => $l['fecha_vencimiento'],
                    'cantidad' => $l['cantidad'],
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }
        }
    }
};
