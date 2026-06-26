<?php
declare(strict_types=1);

namespace App\Models;

use App\Support\Database;
use App\Support\OrdenHelper;
use PDO;

final class Entradas
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    /** Registra una entrada (compra/recepción) de producto */
    public function create(array $data): int
    {
        $cols = 'inventario_id, codigo, cantidad, quien_entrego, quien_recibio, observaciones, fecha_entrada, hora_entrada';
        $vals = ':inventario_id, :codigo, :cantidad, :quien_entrego, :quien_recibio, :observaciones, :fecha_entrada, :hora_entrada';
        $params = [
            ':inventario_id' => (int)$data['inventario_id'],
            ':codigo' => $data['codigo'],
            ':cantidad' => (int)$data['cantidad'],
            ':quien_entrego' => $data['quien_entrego'] ?: null,
            ':quien_recibio' => $data['quien_recibio'] ?: null,
            ':observaciones' => $data['observaciones'] ?: null,
            ':fecha_entrada' => $data['fecha_entrada'] ?: null,
            ':hora_entrada' => $data['hora_entrada'] ?: null,
        ];

        if (OrdenHelper::schemaReady() && !empty($data['orden_entrada_id'])) {
            $cols .= ', orden_entrada_id';
            $vals .= ', :orden_entrada_id';
            $params[':orden_entrada_id'] = (int)$data['orden_entrada_id'];
        }

        $sql = "INSERT INTO entradas ({$cols}) VALUES ({$vals})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$this->db->lastInsertId();
    }

    /** Obtiene todas las entradas de un producto */
    public function byInventarioId(int $inventarioId): array
    {
        if (OrdenHelper::schemaReady()) {
            $sql = 'SELECT e.*, oe.numero AS orden_numero
                    FROM entradas e
                    LEFT JOIN ordenes_entrada oe ON e.orden_entrada_id = oe.id
                    WHERE e.inventario_id = :id
                    ORDER BY e.created_at DESC';
        } else {
            $sql = 'SELECT * FROM entradas WHERE inventario_id = :id ORDER BY created_at DESC';
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
            $where[] = '(i.codigo LIKE :q1 OR i.nombre LIKE :q2 OR e.observaciones LIKE :q3)';
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
                $where[] = 'oe.numero = :orden';
                $params[':orden'] = (int)$orden;
            }
        }
        if (($filters['desde'] ?? '') !== '') {
            $where[] = 'COALESCE(e.fecha_entrada, DATE(e.created_at)) >= :desde';
            $params[':desde'] = $filters['desde'];
        }
        if (($filters['hasta'] ?? '') !== '') {
            $where[] = 'COALESCE(e.fecha_entrada, DATE(e.created_at)) <= :hasta';
            $params[':hasta'] = $filters['hasta'];
        }

        $sql = 'SELECT e.*, i.codigo, i.nombre, i.unidad, oe.numero AS orden_numero, oe.id AS orden_id
                FROM entradas e
                LEFT JOIN inventario i ON e.inventario_id = i.id
                LEFT JOIN ordenes_entrada oe ON e.orden_entrada_id = oe.id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY e.created_at DESC';

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
            $where[] = 'COALESCE(e.fecha_entrada, DATE(e.created_at)) >= :desde';
            $params[':desde'] = $filters['desde'];
        }
        if (($filters['hasta'] ?? '') !== '') {
            $where[] = 'COALESCE(e.fecha_entrada, DATE(e.created_at)) <= :hasta';
            $params[':hasta'] = $filters['hasta'];
        }
        $sql = 'SELECT e.*, i.codigo, i.nombre, i.unidad, NULL AS orden_numero
                FROM entradas e
                LEFT JOIN inventario i ON e.inventario_id = i.id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY e.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM entradas WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM entradas WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public function totalEntradasPorProducto(int $inventarioId): int
    {
        $stmt = $this->db->prepare('SELECT COALESCE(SUM(cantidad), 0) as total FROM entradas WHERE inventario_id = :id');
        $stmt->execute([':id' => $inventarioId]);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }
}
