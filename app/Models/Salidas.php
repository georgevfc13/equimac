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

    public function create(int $inventarioId, string $codigo, string $quienRecibio, string $quienEntrego, int $cantidadUsada): void
    {
        $sql = 'INSERT INTO salidas (inventario_id, codigo, quien_recibio, quien_entrego, cantidad_usada)
                VALUES (:inv_id, :codigo, :quien_recibio, :quien_entrego, :cantidad_usada)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':inv_id' => $inventarioId,
            ':codigo' => $codigo,
            ':quien_recibio' => $quienRecibio,
            ':quien_entrego' => $quienEntrego,
            ':cantidad_usada' => $cantidadUsada,
        ]);
    }
}
