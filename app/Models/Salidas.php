<?php
declare(strict_types=1);

namespace App\Models;

use App\Support\Database;
use App\Support\OrdenHelper;
use PDO;

final class Salidas
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function create(
        int $inventarioId,
        string $codigo,
        string $quienRecibio,
        string $quienEntrego,
        int $cantidadUsada,
        ?string $fechaSalida = null,
        ?string $horaSalida = null,
        ?string $observaciones = null,
        ?int $ordenSalidaId = null
    ): void {
        $cols = 'inventario_id, codigo, quien_recibio, quien_entrego, cantidad_usada, fecha_salida, hora_salida, observaciones';
        $vals = ':inv_id, :codigo, :quien_recibio, :quien_entrego, :cantidad_usada, :fecha_salida, :hora_salida, :observaciones';
        $params = [
            ':inv_id' => $inventarioId,
            ':codigo' => $codigo,
            ':quien_recibio' => $quienRecibio,
            ':quien_entrego' => $quienEntrego,
            ':cantidad_usada' => $cantidadUsada,
            ':fecha_salida' => $fechaSalida ?: null,
            ':hora_salida' => $horaSalida ?: null,
            ':observaciones' => $observaciones ?: null,
        ];

        if (OrdenHelper::schemaReady() && $ordenSalidaId) {
            $cols .= ', orden_salida_id';
            $vals .= ', :orden_salida_id';
            $params[':orden_salida_id'] = $ordenSalidaId;
        }

        $sql = "INSERT INTO salidas ({$cols}) VALUES ({$vals})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    public function byInventarioId(int $inventarioId): array
    {
        if (OrdenHelper::schemaReady()) {
            $sql = 'SELECT s.*, os.numero AS orden_numero
                    FROM salidas s
                    LEFT JOIN ordenes_salida os ON s.orden_salida_id = os.id
                    WHERE s.inventario_id = :id
                    ORDER BY s.created_at DESC';
        } else {
            $sql = 'SELECT * FROM salidas WHERE inventario_id = :id ORDER BY created_at DESC';
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $inventarioId]);
        return $stmt->fetchAll() ?: [];
    }

    /** @param array{q?:string,orden?:string,desde?:string,hasta?:string} $filters */
    public function searchAll(array $filters = []): array
    {
        if (!OrdenHelper::schemaReady()) {
            return $this->legacySearchAll($filters);
        }

        $where = ['1=1'];
        $params = [];

        if (($filters['q'] ?? '') !== '') {
            $where[] = '(i.codigo LIKE :q1 OR i.nombre LIKE :q2 OR s.observaciones LIKE :q3)';
            $like = '%' . $filters['q'] . '%';
            $params[':q1'] = $like;
            $params[':q2'] = $like;
            $params[':q3'] = $like;
        }
        if (($filters['orden'] ?? '') !== '') {
            $orden = ltrim(trim($filters['orden']), '0');
            if ($orden === '') {
                $orden = '0';
            }
            if (is_numeric($orden)) {
                $where[] = 'os.numero = :orden';
                $params[':orden'] = (int)$orden;
            }
        }
        if (($filters['desde'] ?? '') !== '') {
            $where[] = 'COALESCE(s.fecha_salida, DATE(s.created_at)) >= :desde';
            $params[':desde'] = $filters['desde'];
        }
        if (($filters['hasta'] ?? '') !== '') {
            $where[] = 'COALESCE(s.fecha_salida, DATE(s.created_at)) <= :hasta';
            $params[':hasta'] = $filters['hasta'];
        }

        $sql = 'SELECT s.*, i.codigo, i.nombre, i.unidad, os.numero AS orden_numero, os.id AS orden_id
                FROM salidas s
                LEFT JOIN inventario i ON s.inventario_id = i.id
                LEFT JOIN ordenes_salida os ON s.orden_salida_id = os.id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY s.created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    /** @param array{q?:string,desde?:string,hasta?:string} $filters */
    private function legacySearchAll(array $filters): array
    {
        $where = ['1=1'];
        $params = [];
        if (($filters['q'] ?? '') !== '') {
            $where[] = '(i.codigo LIKE :q1 OR i.nombre LIKE :q2)';
            $like = '%' . $filters['q'] . '%';
            $params[':q1'] = $like;
            $params[':q2'] = $like;
        }
        if (($filters['desde'] ?? '') !== '') {
            $where[] = 'COALESCE(s.fecha_salida, DATE(s.created_at)) >= :desde';
            $params[':desde'] = $filters['desde'];
        }
        if (($filters['hasta'] ?? '') !== '') {
            $where[] = 'COALESCE(s.fecha_salida, DATE(s.created_at)) <= :hasta';
            $params[':hasta'] = $filters['hasta'];
        }
        $sql = 'SELECT s.*, i.codigo, i.nombre, i.unidad, NULL AS orden_numero
                FROM salidas s
                LEFT JOIN inventario i ON s.inventario_id = i.id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY s.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM salidas WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function totalSalidasPorProducto(int $inventarioId): int
    {
        $stmt = $this->db->prepare('SELECT COALESCE(SUM(cantidad_usada), 0) as total FROM salidas WHERE inventario_id = :id');
        $stmt->execute([':id' => $inventarioId]);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM salidas WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
