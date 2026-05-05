<?php
declare(strict_types=1);

namespace App\Support;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo) {
            return self::$pdo;
        }

        $host = getenv('EQUIMAC_DB_HOST') ?: '127.0.0.1';
        $port = getenv('EQUIMAC_DB_PORT') ?: '3306';
        $db   = getenv('EQUIMAC_DB_NAME') ?: 'equimac';
        $user = getenv('EQUIMAC_DB_USER') ?: 'root';
        $pass = getenv('EQUIMAC_DB_PASS') ?: '';

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

        try {
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            // Local app: show friendly error page.
            throw new \RuntimeException('No se pudo conectar a la base de datos. Revisa XAMPP/MySQL y el nombre de la BD. Detalle: ' . $e->getMessage());
        }

        return self::$pdo;
    }
}

