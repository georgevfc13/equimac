<?php
/**
 * Modelo: Inventario
 * Archivo: models/Inventario.php
 * Responsabilidad: Gestionar todas las operaciones de BD para inventario
 */

require_once __DIR__ . '/../config/database.php';

class Inventario {
    private $db;
    private $table = 'inventario';
    private $table_estantes = 'estantes';

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * CRUD: CREATE - Crear un nuevo producto
     */
    public function crear($datos) {
        $sql = "INSERT INTO {$this->table} 
                (codigo, descripcion, unidad, cantidad, marca, equipo, aplicacion, estante, entrepaño, estado, tipo_maquinaria) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['error' => 'Error en preparación: ' . $this->db->error];
        }

        $resultado = $stmt->bind_param(
            'sssisssiiis',
            $datos['codigo'],
            $datos['descripcion'],
            $datos['unidad'],
            $datos['cantidad'],
            $datos['marca'],
            $datos['equipo'],
            $datos['aplicacion'],
            $datos['estante'],
            $datos['entrepaño'],
            $datos['estado'],
            $datos['tipo_maquinaria']
        );

        if (!$resultado || !$stmt->execute()) {
            return ['error' => 'Error al crear: ' . $stmt->error];
        }

        $stmt->close();
        return ['exito' => true, 'id' => $this->db->insert_id];
    }

    /**
     * CRUD: READ - Obtener todos los productos
     */
    public function obtenerTodos($filtro = null) {
        $sql = "SELECT * FROM {$this->table}";

        if ($filtro) {
            $sql .= " WHERE codigo LIKE ? OR descripcion LIKE ? OR marca LIKE ? OR equipo LIKE ?";
        }

        $sql .= " ORDER BY estante ASC, entrepaño ASC, codigo ASC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return [];
        }

        if ($filtro) {
            $busqueda = "%{$filtro}%";
            $stmt->bind_param('ssss', $busqueda, $busqueda, $busqueda, $busqueda);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();
        $productos = [];

        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }

        $stmt->close();
        return $productos;
    }

    /**
     * CRUD: READ - Obtener un producto por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $producto = $resultado->fetch_assoc();
        $stmt->close();

        return $producto;
    }

    /**
     * CRUD: UPDATE - Actualizar un producto
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE {$this->table} 
                SET descripcion = ?, unidad = ?, cantidad = ?, marca = ?, 
                    equipo = ?, aplicacion = ?, estante = ?, entrepaño = ?, 
                    estado = ?, tipo_maquinaria = ?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['error' => 'Error en preparación: ' . $this->db->error];
        }

        $resultado = $stmt->bind_param(
            'sssisssiiis',
            $datos['descripcion'],
            $datos['unidad'],
            $datos['cantidad'],
            $datos['marca'],
            $datos['equipo'],
            $datos['aplicacion'],
            $datos['estante'],
            $datos['entrepaño'],
            $datos['estado'],
            $datos['tipo_maquinaria'],
            $id
        );

        if (!$resultado || !$stmt->execute()) {
            return ['error' => 'Error al actualizar: ' . $stmt->error];
        }

        $stmt->close();
        return ['exito' => true];
    }

    /**
     * CRUD: DELETE - Eliminar un producto
     */
    public function eliminar($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['error' => 'Error en preparación: ' . $this->db->error];
        }

        $stmt->bind_param('i', $id);
        if (!$stmt->execute()) {
            return ['error' => 'Error al eliminar: ' . $stmt->error];
        }

        $stmt->close();
        return ['exito' => true];
    }

    /**
     * Obtener todas las marcas únicas
     */
    public function obtenerMarcas() {
        $sql = "SELECT DISTINCT marca FROM {$this->table} 
                WHERE marca IS NOT NULL AND marca != '' 
                ORDER BY marca ASC";

        $resultado = $this->db->query($sql);
        $marcas = [];

        while ($fila = $resultado->fetch_assoc()) {
            $marcas[] = $fila['marca'];
        }

        return $marcas;
    }

    /**
     * Obtener todos los tipos de máquinaria
     */
    public function obtenerTiposMaquinaria() {
        $sql = "SELECT DISTINCT tipo_maquinaria FROM {$this->table} 
                WHERE tipo_maquinaria IS NOT NULL AND tipo_maquinaria != '' 
                ORDER BY tipo_maquinaria ASC";

        $resultado = $this->db->query($sql);
        $tipos = [];

        while ($fila = $resultado->fetch_assoc()) {
            $tipos[] = $fila['tipo_maquinaria'];
        }

        return $tipos;
    }

    /**
     * Obtener todas las unidades de medida
     */
    public function obtenerUnidades() {
        $unidades = ['Pieza', 'Unidad', 'Metro', 'Kilogramo', 'Litro', 'Caja', 'Paquete', 'Rollo', 'Bobina', 'Set'];
        return $unidades;
    }

    /**
     * Obtener todos los estantes
     */
    public function obtenerEstantes() {
        $sql = "SELECT * FROM {$this->table_estantes} ORDER BY numero ASC";
        $resultado = $this->db->query($sql);
        $estantes = [];

        while ($fila = $resultado->fetch_assoc()) {
            $estantes[] = $fila;
        }

        return $estantes;
    }

    /**
     * Obtener un estante por número
     */
    public function obtenerEstante($numero) {
        $sql = "SELECT * FROM {$this->table_estantes} WHERE numero = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param('i', $numero);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $estante = $resultado->fetch_assoc();
        $stmt->close();

        return $estante;
    }

    /**
     * Obtener estadísticas del inventario
     */
    public function obtenerEstadisticas() {
        $sql = "SELECT 
                COUNT(*) as total_productos,
                SUM(cantidad) as cantidad_total,
                COUNT(DISTINCT estante) as total_estantes,
                COUNT(DISTINCT marca) as total_marcas
                FROM {$this->table}";

        $resultado = $this->db->query($sql);
        return $resultado->fetch_assoc();
    }

    /**
     * Obtener productos por estante y entrepaño
     */
    public function obtenerProductosPorUbicacion($estante, $entrepaño) {
        $sql = "SELECT * FROM {$this->table} WHERE estante = ? AND entrepaño = ? ORDER BY codigo ASC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param('ii', $estante, $entrepaño);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $productos = [];

        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }

        $stmt->close();
        return $productos;
    }

    /**
     * Verificar si un código ya existe
     */
    public function codigoExiste($codigo, $id_excluir = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE codigo = ?";
        
        if ($id_excluir) {
            $sql .= " AND id != ?";
        }

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return false;
        }

        if ($id_excluir) {
            $stmt->bind_param('si', $codigo, $id_excluir);
        } else {
            $stmt->bind_param('s', $codigo);
        }

        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $resultado['total'] > 0;
    }
}
?>