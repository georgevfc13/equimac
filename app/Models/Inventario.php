<?php
declare(strict_types=1);

namespace App\Models;

use App\Support\Database;
use PDO;

final class Inventario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function stats(): array
    {
        $sql = "SELECT
                  COUNT(*) AS total_productos,
                  COALESCE(SUM(cantidad), 0) AS cantidad_total,
                  COUNT(DISTINCT estante) AS total_estantes,
                  COUNT(DISTINCT marca) AS total_marcas
                FROM inventario";
        return $this->db->query($sql)->fetch() ?: [
            'total_productos' => 0,
            'cantidad_total' => 0,
            'total_estantes' => 0,
            'total_marcas' => 0,
        ];
    }

    public function all(string $q = ''): array
    {
        if ($q === '') {
            $stmt = $this->db->query("SELECT * FROM inventario ORDER BY estante, entrepaño, posicion, codigo");
            return $stmt->fetchAll() ?: [];
        }

        $sql = "SELECT * FROM inventario
                WHERE codigo LIKE :q OR descripcion LIKE :q OR marca LIKE :q OR equipo LIKE :q
                ORDER BY estante, entrepaño, posicion, codigo";
        $stmt = $this->db->prepare($sql);
        $like = '%' . $q . '%';
        $stmt->execute([':q' => $like]);
        return $stmt->fetchAll() ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM inventario WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByCodigo(string $codigo): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM inventario WHERE codigo = :c LIMIT 1');
        $stmt->execute([':c' => $codigo]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Descuenta cantidad si hay stock suficiente. */
    public function decrementCantidad(int $id, int $cantidad): bool
    {
        if ($cantidad <= 0) {
            return false;
        }
        $sql = 'UPDATE inventario
                SET cantidad = cantidad - :qty, fecha_actualizacion = NOW()
                WHERE id = :id AND cantidad >= :min_stock';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':qty' => $cantidad,
            ':id' => $id,
            ':min_stock' => $cantidad,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO inventario
                (codigo, descripcion, unidad, cantidad, marca, equipo, aplicacion, estante, entrepaño, posicion, estado, tipo_maquinaria, de_quien_llego, precio_pagado, quien_recibio, fecha_creacion, fecha_actualizacion)
                VALUES
                (:codigo, :descripcion, :unidad, :cantidad, :marca, :equipo, :aplicacion, :estante, :entrepano, :posicion, :estado, :tipo_maquinaria, :de_quien_llego, :precio_pagado, :quien_recibio, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':codigo' => $data['codigo'],
            ':descripcion' => $data['descripcion'],
            ':unidad' => $data['unidad'],
            ':cantidad' => (int)$data['cantidad'],
            ':marca' => $data['marca'] ?: null,
            ':equipo' => $data['equipo'] ?: null,
            ':aplicacion' => $data['aplicacion'] ?: null,
            ':estante' => (int)$data['estante'],
            // Placeholder ASCII-only: PDO truncates :entrepaño at "ñ" when emulating prepares.
            ':entrepano' => (int)$data['entrepaño'],
            ':posicion' => (int)$data['posicion'],
            ':estado' => $data['estado'] ?: null,
            ':tipo_maquinaria' => $data['tipo_maquinaria'] ?: null,
            ':de_quien_llego' => $data['de_quien_llego'] ?: null,
            ':precio_pagado' => ($data['precio_pagado'] === '' || $data['precio_pagado'] === null) ? null : (float)$data['precio_pagado'],
            ':quien_recibio' => $data['quien_recibio'] ?: null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = "UPDATE inventario SET
                  descripcion = :descripcion,
                  unidad = :unidad,
                  cantidad = :cantidad,
                  marca = :marca,
                  equipo = :equipo,
                  aplicacion = :aplicacion,
                  estante = :estante,
                  entrepaño = :entrepano,
                  posicion = :posicion,
                  estado = :estado,
                  tipo_maquinaria = :tipo_maquinaria,
                  de_quien_llego = :de_quien_llego,
                  precio_pagado = :precio_pagado,
                  quien_recibio = :quien_recibio,
                  fecha_actualizacion = NOW()
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':descripcion' => $data['descripcion'],
            ':unidad' => $data['unidad'],
            ':cantidad' => (int)$data['cantidad'],
            ':marca' => $data['marca'] ?: null,
            ':equipo' => $data['equipo'] ?: null,
            ':aplicacion' => $data['aplicacion'] ?: null,
            ':estante' => (int)$data['estante'],
            ':entrepano' => (int)$data['entrepaño'],
            ':posicion' => (int)$data['posicion'],
            ':estado' => $data['estado'] ?: null,
            ':tipo_maquinaria' => $data['tipo_maquinaria'] ?: null,
            ':de_quien_llego' => $data['de_quien_llego'] ?: null,
            ':precio_pagado' => ($data['precio_pagado'] === '' || $data['precio_pagado'] === null) ? null : (float)$data['precio_pagado'],
            ':quien_recibio' => $data['quien_recibio'] ?: null,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM inventario WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public function codigoExists(string $codigo, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) AS c FROM inventario WHERE codigo = :c AND id <> :id");
            $stmt->execute([':c' => $codigo, ':id' => $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) AS c FROM inventario WHERE codigo = :c");
            $stmt->execute([':c' => $codigo]);
        }
        $row = $stmt->fetch();
        return ((int)($row['c'] ?? 0)) > 0;
    }

    public function posicionesOcupadas(int $estante, int $entrepaño): array
    {
        $stmt = $this->db->prepare("SELECT posicion, codigo, descripcion FROM inventario WHERE estante = :e AND entrepaño = :f ORDER BY posicion");
        $stmt->execute([':e' => $estante, ':f' => $entrepaño]);
        return $stmt->fetchAll() ?: [];
    }
}

