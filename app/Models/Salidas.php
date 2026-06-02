<?php
declare(strict_types=1);

namespace App\Models;

use App\Support\Database;
use PDO;

final class Salidas
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function create(int $inventarioId, string $codigo, string $quienRecibio, string $quienEntrego, int $cantidadUsada, ?string $fechaSalida = null, ?string $horaSalida = null): void
    {
        $sql = 'INSERT INTO salidas (inventario_id, codigo, quien_recibio, quien_entrego, cantidad_usada, fecha_salida, hora_salida)
                VALUES (:inv_id, :codigo, :quien_recibio, :quien_entrego, :cantidad_usada, :fecha_salida, :hora_salida)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':inv_id' => $inventarioId,
            ':codigo' => $codigo,
            ':quien_recibio' => $quienRecibio,
            ':quien_entrego' => $quienEntrego,
            ':cantidad_usada' => $cantidadUsada,
            ':fecha_salida' => $fechaSalida ?: null,
            ':hora_salida' => $horaSalida ?: null,
        ]);
    }

    /** Obtiene todas las salidas de un producto */
    public function byInventarioId(int $inventarioId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM salidas WHERE inventario_id = :id ORDER BY created_at DESC");
        $stmt->execute([':id' => $inventarioId]);
        return $stmt->fetchAll() ?: [];
    }

    /** Obtiene una salida específica */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM salidas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Obtiene el total de cantidad salida de un producto */
    public function totalSalidasPorProducto(int $inventarioId): int
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(cantidad_usada), 0) as total FROM salidas WHERE inventario_id = :id");
        $stmt->execute([':id' => $inventarioId]);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    /** Elimina una salida */
    public function delete(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM salidas WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}
