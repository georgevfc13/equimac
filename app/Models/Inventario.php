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

    /** Lista ligera para selects (reabastecimiento, etc.). */
    public function allForSelect(): array
    {
        $sql = "SELECT id, codigo, nombre, cantidad, unidad, stock_minimo, estante, entrepaño, posicion
                FROM inventario
                ORDER BY nombre ASC, codigo ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll() ?: [];
    }

    public function all(string $q = ''): array
    {
        return $this->search($q, 'all');
    }

    public function search(string $q = '', string $type = 'all'): array
    {
        $order = ' ORDER BY estante, entrepaño, posicion, nombre, codigo';

        if ($q === '') {
            $stmt = $this->db->query('SELECT * FROM inventario' . $order);
            return $stmt->fetchAll() ?: [];
        }

        $fields = match ($type) {
            'codigo' => ['codigo'],
            'nombre' => ['nombre'],
            'marca' => ['marca'],
            'equipo' => ['equipo'],
            'tipo_maquinaria' => ['tipo_maquinaria'],
            'descripcion' => ['descripcion'],
            default => ['codigo', 'nombre', 'descripcion', 'marca', 'equipo', 'tipo_maquinaria'],
        };

        [$where, $params] = $this->buildLikeWhere($fields, $q);
        $stmt = $this->db->prepare('SELECT * FROM inventario WHERE ' . $where . $order);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    /** @param list<string> $fields */
    private function buildLikeWhere(array $fields, string $q): array
    {
        $like = '%' . $q . '%';
        $parts = [];
        $params = [];
        foreach ($fields as $i => $field) {
            $key = ':q' . ($i + 1);
            $parts[] = $field . ' LIKE ' . $key;
            $params[$key] = $like;
        }

        return [implode(' OR ', $parts), $params];
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

    /** Incrementa cantidad de un producto. */
    public function incrementCantidad(int $id, int $cantidad): bool
    {
        if ($cantidad <= 0) {
            return false;
        }
        $sql = 'UPDATE inventario
                SET cantidad = cantidad + :qty, fecha_actualizacion = NOW()
                WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':qty' => $cantidad,
            ':id' => $id,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO inventario
                (codigo, nombre, descripcion, unidad, cantidad, stock_minimo, marca, equipo, aplicacion, estante, entrepaño, posicion, estado, tipo_maquinaria, de_quien_llego, precio_pagado, quien_recibio, fecha_creacion, fecha_actualizacion)
                VALUES
                (:codigo, :nombre, :descripcion, :unidad, :cantidad, :stock_minimo, :marca, :equipo, :aplicacion, :estante, :entrepano, :posicion, :estado, :tipo_maquinaria, :de_quien_llego, :precio_pagado, :quien_recibio, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':codigo' => $data['codigo'],
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':unidad' => $data['unidad'],
            ':cantidad' => (int)$data['cantidad'],
            ':stock_minimo' => (int)($data['stock_minimo'] ?? 5),
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
                  nombre = :nombre,
                  descripcion = :descripcion,
                  unidad = :unidad,
                  cantidad = :cantidad,
                  stock_minimo = :stock_minimo,
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
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':unidad' => $data['unidad'],
            ':cantidad' => (int)$data['cantidad'],
            ':stock_minimo' => (int)($data['stock_minimo'] ?? 5),
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
        $stmt = $this->db->prepare("SELECT posicion, codigo, nombre FROM inventario WHERE estante = :e AND entrepaño = :f ORDER BY posicion");
        $stmt->execute([':e' => $estante, ':f' => $entrepaño]);
        return $stmt->fetchAll() ?: [];
    }

    /** @return array<int, array<int, int>> Conteo de productos por fila/posición (varios permitidos). */
    public function posicionesOcupadasPorEstante(int $estante): array
    {
        $stmt = $this->db->prepare(
            'SELECT entrepaño, posicion, COUNT(*) AS total
             FROM inventario WHERE estante = :e
             GROUP BY entrepaño, posicion'
        );
        $stmt->execute([':e' => $estante]);
        $rows = $stmt->fetchAll() ?: [];

        $result = [];
        foreach ($rows as $row) {
            $fila = (int)$row['entrepaño'];
            $pos = (int)$row['posicion'];
            if (!isset($result[$fila])) {
                $result[$fila] = [];
            }
            $result[$fila][$pos] = (int)$row['total'];
        }
        return $result;
    }

    /** Obtiene productos con stock bajo o en cero */
    public function lowStockItems(): array
    {
        $sql = "SELECT id, codigo, nombre, cantidad, stock_minimo 
                FROM inventario 
                WHERE cantidad <= stock_minimo 
                ORDER BY cantidad ASC, nombre, codigo";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll() ?: [];
    }

    /** Obtiene productos con stock en cero */
    public function outOfStockItems(): array
    {
        $sql = "SELECT id, codigo, nombre, cantidad 
                FROM inventario 
                WHERE cantidad = 0 
                ORDER BY nombre, codigo";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll() ?: [];
    }
}

