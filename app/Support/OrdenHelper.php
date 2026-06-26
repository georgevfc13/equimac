<?php
declare(strict_types=1);

namespace App\Support;

final class OrdenHelper
{
    public static function formatNumero(int $numero): string
    {
        return str_pad((string)max(0, $numero), 3, '0', STR_PAD_LEFT);
    }

    public static function labelEntrada(int $numero): string
    {
        return 'Orden de entrada ' . self::formatNumero($numero);
    }

    public static function labelSalida(int $numero): string
    {
        return 'Orden de salida ' . self::formatNumero($numero);
    }

    public static function schemaReady(): bool
    {
        static $ready = null;
        if ($ready !== null) {
            return $ready;
        }
        try {
            $db = Database::pdo();
            $db->query('SELECT 1 FROM ordenes_entrada LIMIT 1');
            $db->query('SELECT 1 FROM ordenes_salida LIMIT 1');
            $ready = true;
        } catch (\Throwable $e) {
            $ready = false;
        }
        return $ready;
    }
}
