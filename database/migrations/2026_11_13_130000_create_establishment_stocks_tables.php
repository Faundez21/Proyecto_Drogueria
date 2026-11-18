<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // saldo de cada producto en cada establecimiento de la red
        Schema::create('establishment_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('establecimiento');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('producto', 1000);
            $table->integer('cantidad')->default(0);
            $table->timestamps();
            $table->unique(['establecimiento', 'product_id']);
        });

        // historial de cada cambio de saldo
        Schema::create('establishment_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_stock_id')->constrained('establishment_stocks')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo', 30);
            $table->integer('cantidad');
            $table->integer('saldo');
            $table->string('referencia', 500)->nullable();
            $table->foreignId('dispatch_id')->nullable()->constrained('dispatches')->nullOnDelete();
            $table->foreignId('establishment_request_id')->nullable()->constrained('establishment_requests')->nullOnDelete();
            $table->timestamps();
        });

        // establecimiento desde donde sale un despacho interno
        Schema::table('dispatches', function (Blueprint $table) {
            $table->string('establecimiento_origen')->nullable()->after('clasificacion');
        });

        $this->cargarSaldosIniciales();
    }

    public function down(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropColumn('establecimiento_origen');
        });
        Schema::dropIfExists('establishment_stock_movements');
        Schema::dropIfExists('establishment_stocks');
    }

    // saldo inicial: ultimo stock declarado en solicitudes mas lo despachado despues
    private function cargarSaldosIniciales(): void
    {
        $nombreCanonico = [];
        foreach (config('drogueria.establecimientos', []) as $est) {
            $nombreCanonico[mb_strtolower(trim($est['nombre']))] = $est['nombre'];
        }
        $canonico = fn ($nombre) => $nombreCanonico[mb_strtolower(trim((string) $nombre))] ?? trim((string) $nombre);

        $declarados = DB::table('establishment_items as i')
            ->join('establishment_requests as r', 'r.id', '=', 'i.establishment_request_id')
            ->leftJoin('products as p', 'p.id', '=', 'i.product_id')
            ->whereNotNull('i.stock_actual')
            ->orderByDesc('i.id')
            ->get(['i.product_id', 'i.stock_actual', 'r.establecimiento', 'r.id as request_id', 'r.created_at', 'p.nombre']);

        $vistos = [];
        $ahora = now();
        foreach ($declarados as $fila) {
            $est = $canonico($fila->establecimiento);
            $clave = mb_strtolower($est) . '|' . $fila->product_id;
            if ($est === '' || ! $fila->nombre || isset($vistos[$clave])) {
                continue;
            }
            $vistos[$clave] = true;

            // lo que la drogueria le despacho despues de esa declaracion
            $recibido = (int) DB::table('dispatch_items as di')
                ->join('dispatches as d', 'd.id', '=', 'di.dispatch_id')
                ->where('d.clasificacion', 'Externo')
                ->whereRaw('LOWER(TRIM(d.establecimiento_destino)) = ?', [mb_strtolower($est)])
                ->where('di.product_id', $fila->product_id)
                ->where('d.created_at', '>', $fila->created_at)
                ->sum('di.cantidad');
            $recibido += (int) DB::table('controlled_dispatch_items as ci')
                ->join('controlled_dispatches as c', 'c.id', '=', 'ci.controlled_dispatch_id')
                ->whereRaw('LOWER(TRIM(c.establecimiento_destino)) = ?', [mb_strtolower($est)])
                ->where('ci.product_id', $fila->product_id)
                ->where('c.created_at', '>', $fila->created_at)
                ->sum('ci.cantidad');

            $saldo = (int) $fila->stock_actual + $recibido;
            $stockId = DB::table('establishment_stocks')->insertGetId([
                'establecimiento' => $est,
                'product_id' => $fila->product_id,
                'producto' => $fila->nombre,
                'cantidad' => $saldo,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
            DB::table('establishment_stock_movements')->insert([
                'establishment_stock_id' => $stockId,
                'tipo' => 'Saldo inicial',
                'cantidad' => $saldo,
                'saldo' => $saldo,
                'referencia' => 'Último stock declarado en solicitud' . ($recibido ? " + {$recibido} uds. despachadas después" : ''),
                'establishment_request_id' => $fila->request_id,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }
    }
};
