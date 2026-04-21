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

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * CRUD: CREATE - Crear un nuevo producto
     */
    public function crear($datos) {
        $sql = "INSERT INTO {$this->table} 
                (codigo, nombre, descripcion, cantidad, precio_unitario, categoria, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['error' => 'Error en preparación: ' . $this->db->error];
        }

        $resultado = $stmt->bind_param(
            'sssidss',
            $datos['codigo'],
            $datos['nombre'],
            $datos['descripcion'],
            $datos['cantidad'],
            $datos['precio_unitario'],
            $datos['categoria'],
            $datos['estado']
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
            $sql .= " WHERE nombre LIKE ? OR codigo LIKE ? OR categoria LIKE ?";
        }

        $sql .= " ORDER BY fecha_creacion DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return [];
        }

        if ($filtro) {
            $busqueda = "%{$filtro}%";
            $stmt->bind_param('sss', $busqueda, $busqueda, $busqueda);
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
                SET nombre = ?, descripcion = ?, cantidad = ?, 
                    precio_unitario = ?, categoria = ?, estado = ?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['error' => 'Error en preparación: ' . $this->db->error];
        }

        $resultado = $stmt->bind_param(
            'sssidsi',
            $datos['nombre'],
            $datos['descripcion'],
            $datos['cantidad'],
            $datos['precio_unitario'],
            $datos['categoria'],
            $datos['estado'],
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
     * Obtener categorías únicas
     */
    public function obtenerCategorias() {
        $sql = "SELECT DISTINCT categoria FROM {$this->table} 
                WHERE categoria IS NOT NULL AND categoria != '' 
                ORDER BY categoria ASC";

        $resultado = $this->db->query($sql);
        $categorias = [];

        while ($fila = $resultado->fetch_assoc()) {
            $categorias[] = $fila['categoria'];
        }

        return $categorias;
    }

    /**
     * Obtener estadísticas del inventario
     */
    public function obtenerEstadisticas() {
        $sql = "SELECT 
                COUNT(*) as total_productos,
                SUM(cantidad) as cantidad_total,
                SUM(cantidad * precio_unitario) as valor_total,
                AVG(precio_unitario) as precio_promedio
                FROM {$this->table} WHERE estado = 'activo'";

        $resultado = $this->db->query($sql);
        return $resultado->fetch_assoc();
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
