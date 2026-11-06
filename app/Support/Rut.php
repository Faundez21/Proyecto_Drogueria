<?php

namespace App\Support;

/**
 * Utilidades para RUT chileno: limpiar, validar dígito verificador y dar formato.
 */
class Rut
{
    /** Deja solo números y K: "76.543.210-k" -> "76543210K". */
    public static function limpiar(?string $rut): string
    {
        return strtoupper((string) preg_replace('/[^0-9kK]/', '', (string) $rut));
    }

    /** Calcula el dígito verificador de un cuerpo numérico. */
    public static function digitoVerificador(string $cuerpo): string
    {
        $suma = 0;
        $multiplo = 2;
        for ($i = strlen($cuerpo) - 1; $i >= 0; $i--) {
            $suma += ((int) $cuerpo[$i]) * $multiplo;
            $multiplo = $multiplo === 7 ? 2 : $multiplo + 1;
        }
        $resto = 11 - ($suma % 11);

        return $resto === 11 ? '0' : ($resto === 10 ? 'K' : (string) $resto);
    }

    /** Indica si el RUT tiene un formato y dígito verificador válidos. */
    public static function esValido(?string $rut): bool
    {
        $limpio = self::limpiar($rut);
        if (! preg_match('/^(\d{7,8})([0-9K])$/', $limpio, $m)) {
            return false;
        }

        return self::digitoVerificador($m[1]) === $m[2];
    }

    /** Formato estándar con puntos y guion: "76543210K" -> "76.543.210-K". */
    public static function formatear(?string $rut): string
    {
        $limpio = self::limpiar($rut);
        if (strlen($limpio) < 2) {
            return $limpio;
        }
        $cuerpo = substr($limpio, 0, -1);
        $dv = substr($limpio, -1);

        return number_format((int) $cuerpo, 0, '', '.') . '-' . $dv;
    }
}
