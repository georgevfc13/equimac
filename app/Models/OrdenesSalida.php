<?php
declare(strict_types=1);

namespace App\Models;

use App\Support\Database;
use App\Support\OrdenHelper;
use PDO;

final class OrdenesSalida
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    /** @return array{id:int,numero:int,label:string} */
    public function create(array $data): array
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->query('SELECT COALESCE(MAX(numero), -1) + 1 AS n FROM ordenes_salida FOR UPDATE');
            $numero = (int)($stmt->fetch()['n'] ?? 0);

            $sql = 'INSERT INTO ordenes_salida (numero, quien_recibio, quien_entrego, observaciones, fecha_salida, hora_salida)
                    VALUES (:numero, :quien_recibio, :quien_entrego, :observaciones, :fecha_salida, :hora_salida)';
            $ins = $this->db->prepare($sql);
            $ins->execute([
                ':numero' => $numero,
                ':quien_recibio' => $data['quien_recibio'] ?? '',
                ':quien_entrego' => $data['quien_entrego'] ?? '',
                ':observaciones' => ($data['observaciones'] ?? '') !== '' ? $data['observaciones'] : null,
                ':fecha_salida' => $data['fecha_salida'] ?: null,
                ':hora_salida' => $data['hora_salida'] ?: null,
            ]);
            $id = (int)$this->db->lastInsertId();
            $this->db->commit();

            return [
                'id' => $id,
                'numero' => $numero,
                'label' => OrdenHelper::labelSalida($numero),
            ];
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM ordenes_salida WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
