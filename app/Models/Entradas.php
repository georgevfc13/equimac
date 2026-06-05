<?php
declare(strict_types=1);

namespace App\Models;

use App\Support\Database;
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
        $sql = "INSERT INTO entradas
                (inventario_id, codigo, cantidad, quien_entrego, quien_recibio, observaciones, fecha_entrada, hora_entrada)
                VALUES
                (:inventario_id, :codigo, :cantidad, :quien_entrego, :quien_recibio, :observaciones, :fecha_entrada, :hora_entrada)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':inventario_id' => (int)$data['inventario_id'],
            ':codigo' => $data['codigo'],
            ':cantidad' => (int)$data['cantidad'],
            ':quien_entrego' => $data['quien_entrego'] ?: null,
            ':quien_recibio' => $data['quien_recibio'] ?: null,
            ':observaciones' => $data['observaciones'] ?: null,
            ':fecha_entrada' => $data['fecha_entrada'] ?: null,
            ':hora_entrada' => $data['hora_entrada'] ?: null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Obtiene todas las entradas de un producto */
    public function byInventarioId(int $inventarioId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM entradas WHERE inventario_id = :id ORDER BY created_at DESC");
        $stmt->execute([':id' => $inventarioId]);
        return $stmt->fetchAll() ?: [];
    }

    /** Obtiene una entrada específica */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM entradas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Elimina una entrada */
    public function delete(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM entradas WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    /** Obtiene el total de cantidad ingresada (entradas) de un producto */
    public function totalEntradasPorProducto(int $inventarioId): int
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(cantidad), 0) as total FROM entradas WHERE inventario_id = :id");
        $stmt->execute([':id' => $inventarioId]);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }
}
