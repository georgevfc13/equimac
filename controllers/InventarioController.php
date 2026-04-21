<?php
/**
 * Controlador: InventarioController
 * Archivo: controllers/InventarioController.php
 * Responsabilidad: Procesar peticiones, coordinar modelo y vista
 */

require_once __DIR__ . '/../models/Inventario.php';

class InventarioController {
    private $modelo;
    private $mensaje = null;
    private $tipo_mensaje = null;

    public function __construct() {
        $this->modelo = new Inventario();
    }

    /**
     * Acción: Listar todos los productos
     */
    public function listar() {
        $filtro = $_GET['buscar'] ?? null;
        $productos = $this->modelo->obtenerTodos($filtro);
        $categorias = $this->modelo->obtenerCategorias();
        $estadisticas = $this->modelo->obtenerEstadisticas();

        $this->renderizar('views/lista.php', [
            'productos' => $productos,
            'categorias' => $categorias,
            'estadisticas' => $estadisticas,
            'filtro' => $filtro,
            'mensaje' => $this->mensaje,
            'tipo_mensaje' => $this->tipo_mensaje
        ]);
    }

    /**
     * Acción: Mostrar formulario para crear producto
     */
    public function formulario() {
        $id = $_GET['id'] ?? null;
        $producto = null;

        if ($id) {
            $producto = $this->modelo->obtenerPorId($id);
            if (!$producto) {
                $this->mensaje = 'Producto no encontrado';
                $this->tipo_mensaje = 'error';
                return $this->listar();
            }
        }

        $categorias = $this->modelo->obtenerCategorias();

        $this->renderizar('views/formulario.php', [
            'producto' => $producto,
            'categorias' => $categorias,
            'es_edicion' => $id !== null
        ]);
    }

    /**
     * Acción: Guardar producto (crear o actualizar)
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        $id = $_POST['id'] ?? null;
        $codigo = trim($_POST['codigo'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $cantidad = intval($_POST['cantidad'] ?? 0);
        $precio_unitario = floatval($_POST['precio_unitario'] ?? 0);
        $categoria = trim($_POST['categoria'] ?? '');
        $estado = $_POST['estado'] ?? 'activo';

        // Validaciones
        $errores = $this->validar($codigo, $nombre, $cantidad, $precio_unitario, $id);

        if (!empty($errores)) {
            $this->mensaje = implode('<br>', $errores);
            $this->tipo_mensaje = 'error';
            return $this->formulario();
        }

        // Verificar código único (excepto si es edición del mismo producto)
        if ($this->modelo->codigoExiste($codigo, $id)) {
            $this->mensaje = 'El código de producto ya existe';
            $this->tipo_mensaje = 'error';
            return $this->formulario();
        }

        $datos = [
            'codigo' => $codigo,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'categoria' => $categoria,
            'estado' => $estado
        ];

        if ($id) {
            $resultado = $this->modelo->actualizar($id, $datos);
            $accion = 'actualizado';
        } else {
            $resultado = $this->modelo->crear($datos);
            $accion = 'creado';
        }

        if (isset($resultado['error'])) {
            $this->mensaje = 'Error: ' . $resultado['error'];
            $this->tipo_mensaje = 'error';
            return $this->formulario();
        }

        $this->mensaje = "Producto $accion exitosamente";
        $this->tipo_mensaje = 'exito';
        return $this->listar();
    }

    /**
     * Acción: Eliminar producto
     */
    public function eliminar() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->mensaje = 'ID de producto inválido';
            $this->tipo_mensaje = 'error';
            return $this->listar();
        }

        $producto = $this->modelo->obtenerPorId($id);
        if (!$producto) {
            $this->mensaje = 'Producto no encontrado';
            $this->tipo_mensaje = 'error';
            return $this->listar();
        }

        $resultado = $this->modelo->eliminar($id);

        if (isset($resultado['error'])) {
            $this->mensaje = 'Error: ' . $resultado['error'];
            $this->tipo_mensaje = 'error';
        } else {
            $this->mensaje = "Producto '{$producto['nombre']}' eliminado";
            $this->tipo_mensaje = 'exito';
        }

        return $this->listar();
    }

    /**
     * Validar datos del formulario
     */
    private function validar($codigo, $nombre, $cantidad, $precio_unitario, $id = null) {
        $errores = [];

        if (empty($codigo)) {
            $errores[] = '✗ El código es requerido';
        }

        if (empty($nombre)) {
            $errores[] = '✗ El nombre es requerido';
        }

        if ($cantidad < 0) {
            $errores[] = '✗ La cantidad no puede ser negativa';
        }

        if ($precio_unitario < 0) {
            $errores[] = '✗ El precio no puede ser negativo';
        }

        return $errores;
    }

    /**
     * Renderizar vista con datos
     */
    private function renderizar($vista, $datos = []) {
        extract($datos);
        require $vista;
    }
}
?>
