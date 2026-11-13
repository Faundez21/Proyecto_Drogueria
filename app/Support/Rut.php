<?php

namespace App\Support;

// utilidades para rut chileno: limpiar, validar digito verificador y dar formato
class Rut
{
    // deja solo numeros y k: "76.543.210-k" -> "76543210k"
    public static function limpiar(?string $rut): string
    {
        return strtoupper((string) preg_replace('/[^0-9kK]/', '', (string) $rut));
    }

    // calcula el digito verificador de un cuerpo numerico
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

    // indica si el rut tiene un formato y digito verificador validos
    public static function esValido(?string $rut): bool
    {
        $limpio = self::limpiar($rut);
        if (! preg_match('/^(\d{7,8})([0-9K])$/', $limpio, $m)) {
            return false;
        }

        return self::digitoVerificador($m[1]) === $m[2];
    }

    // formato estandar con puntos y guion: "76543210k" -> "76.543.210-k"
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
