<?php
declare(strict_types=1);

namespace App\Models;

use App\Support\Database;
use PDO;

final class Estantes
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM estantes ORDER BY numero");
        return $stmt->fetchAll() ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM estantes WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByNumero(int $numero): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM estantes WHERE numero = :n");
        $stmt->execute([':n' => $numero]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function numeroExists(int $numero, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $stmt = $this->db->prepare('SELECT COUNT(*) AS c FROM estantes WHERE numero = :n AND id <> :id');
            $stmt->execute([':n' => $numero, ':id' => $excludeId]);
        } else {
            $stmt = $this->db->prepare('SELECT COUNT(*) AS c FROM estantes WHERE numero = :n');
            $stmt->execute([':n' => $numero]);
        }
        $row = $stmt->fetch();
        return ((int)($row['c'] ?? 0)) > 0;
    }

    /** Cuenta productos ubicados en un estante (por número de estante). */
    public function countInventarioByEstanteNumero(int $numero): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS c FROM inventario WHERE estante = :e');
        $stmt->execute([':e' => $numero]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO estantes (numero, descripcion, ubicacion, filas, columnas)
                VALUES (:numero, :descripcion, :ubicacion, :filas, :columnas)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':numero' => (int)$data['numero'],
            ':descripcion' => $data['descripcion'] !== '' ? $data['descripcion'] : null,
            ':ubicacion' => $data['ubicacion'] !== '' ? $data['ubicacion'] : null,
            ':filas' => (int)$data['filas'],
            ':columnas' => (int)$data['columnas'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM estantes WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}

