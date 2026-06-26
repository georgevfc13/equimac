<?php
declare(strict_types=1);

namespace App\Models;

use App\Support\Database;
use App\Support\OrdenHelper;
use PDO;

final class OrdenesEntrada
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
            $stmt = $this->db->query('SELECT COALESCE(MAX(numero), -1) + 1 AS n FROM ordenes_entrada FOR UPDATE');
            $numero = (int)($stmt->fetch()['n'] ?? 0);

            $sql = 'INSERT INTO ordenes_entrada (numero, quien_entrego, quien_recibio, observaciones, fecha_entrada, hora_entrada)
                    VALUES (:numero, :quien_entrego, :quien_recibio, :observaciones, :fecha_entrada, :hora_entrada)';
            $ins = $this->db->prepare($sql);
            $ins->execute([
                ':numero' => $numero,
                ':quien_entrego' => $data['quien_entrego'] ?? '',
                ':quien_recibio' => $data['quien_recibio'] ?? '',
                ':observaciones' => ($data['observaciones'] ?? '') !== '' ? $data['observaciones'] : null,
                ':fecha_entrada' => $data['fecha_entrada'] ?: null,
                ':hora_entrada' => $data['hora_entrada'] ?: null,
            ]);
            $id = (int)$this->db->lastInsertId();
            $this->db->commit();

            return [
                'id' => $id,
                'numero' => $numero,
                'label' => OrdenHelper::labelEntrada($numero),
            ];
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM ordenes_entrada WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
