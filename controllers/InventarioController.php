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
        $estadisticas = $this->modelo->obtenerEstadisticas();

        $this->renderizar('views/lista.php', [
            'productos' => $productos,
            'estadisticas' => $estadisticas,
            'filtro' => $filtro,
            'mensaje' => $this->mensaje,
            'tipo_mensaje' => $this->tipo_mensaje
        ]);
    }

    /**
     * Acción: Ver detalles completos de un producto
     */
    public function detalles() {
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

        // Obtener información del estante
        $estante = $this->modelo->obtenerEstante($producto['estante']);
        
        // Obtener productos en la misma ubicación
        $productosEnUbicacion = $this->modelo->obtenerProductosPorUbicacion(
            $producto['estante'],
            $producto['entrepaño']
        );

        $this->renderizar('views/detalles.php', [
            'producto' => $producto,
            'estante' => $estante,
            'productosEnUbicacion' => $productosEnUbicacion
        ]);
    }

    /**
     * Acción: Mostrar formulario para crear o editar producto
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

        $estantes = $this->modelo->obtenerEstantes();
        $unidades = $this->modelo->obtenerUnidades();
        $marcas = $this->modelo->obtenerMarcas();
        $tipos_maquinaria = $this->modelo->obtenerTiposMaquinaria();

        $this->renderizar('views/formulario.php', [
            'producto' => $producto,
            'estantes' => $estantes,
            'unidades' => $unidades,
            'marcas' => $marcas,
            'tipos_maquinaria' => $tipos_maquinaria,
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
        $descripcion = trim($_POST['descripcion'] ?? '');
        $unidad = trim($_POST['unidad'] ?? '');
        $cantidad = intval($_POST['cantidad'] ?? 0);
        $marca = trim($_POST['marca'] ?? '') ?: null;
        $equipo = trim($_POST['equipo'] ?? '') ?: null;
        $aplicacion = trim($_POST['aplicacion'] ?? '') ?: null;
        $estante = intval($_POST['estante'] ?? 0);
        $entrepaño = intval($_POST['entrepaño'] ?? 0);
        $posicion = intval($_POST['posicion'] ?? 1);
        $estado = trim($_POST['estado'] ?? '') ?: null;
        $tipo_maquinaria = trim($_POST['tipo_maquinaria'] ?? '') ?: null;
        $de_quien_llego = trim($_POST['de_quien_llego'] ?? '') ?: null;
        $precio_pagado = floatval($_POST['precio_pagado'] ?? 0) ?: null;
        $quien_recibio = trim($_POST['quien_recibio'] ?? '') ?: null;

        // Validaciones
        $errores = $this->validar($codigo, $descripcion, $unidad, $cantidad, $estante, $entrepaño, $id);

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
            'descripcion' => $descripcion,
            'unidad' => $unidad,
            'cantidad' => $cantidad,
            'marca' => $marca,
            'equipo' => $equipo,
            'aplicacion' => $aplicacion,
            'estante' => $estante,
            'entrepaño' => $entrepaño,
            'posicion' => $posicion,
            'estado' => $estado,
            'tipo_maquinaria' => $tipo_maquinaria,
            'de_quien_llego' => $de_quien_llego,
            'precio_pagado' => $precio_pagado,
            'quien_recibio' => $quien_recibio
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
            $this->mensaje = "Producto '{$producto['descripcion']}' eliminado";
            $this->tipo_mensaje = 'exito';
        }

        return $this->listar();
    }

    /**
     * Acción: Mostrar visualización de estantes
     */
    public function estantes() {
        $estante_seleccionado = $_GET['estante'] ?? '';
        
        // Obtener todos los productos
        $todosProductos = $this->modelo->obtenerTodos();
        
        // Obtener estantes disponibles
        $estantes_disponibles = [];
        $estantes = [];
        $estantes_stats = [];
        
        // Organizar productos por estante y posición
        foreach ($todosProductos as $producto) {
            $estante_num = $producto['estante'];
            $fila_num = $producto['entrepaño'];
            
            // Registrar estantes disponibles
            if (!in_array($estante_num, $estantes_disponibles)) {
                $estantes_disponibles[] = $estante_num;
            }
            
            // Filtrar si se especificó un estante
            if ($estante_seleccionado && $estante_num != $estante_seleccionado) {
                continue;
            }
            
            // Inicializar estructura
            if (!isset($estantes[$estante_num])) {
                $estantes[$estante_num] = [];
                $estantes_stats[$estante_num] = [
                    'ocupadas' => 0,
                    'libres' => 0,
                    'total_posiciones' => []
                ];
            }
            
            if (!isset($estantes[$estante_num][$fila_num])) {
                $estantes[$estante_num][$fila_num] = [];
            }
            
            // Agregar producto
            $posicion = 1; // Por defecto, posición 1
            $estantes[$estante_num][$fila_num][$posicion] = $producto;
            
            // Estadísticas
            $estantes_stats[$estante_num]['ocupadas']++;
        }
        
        // Crear posiciones vacías (5 por fila)
        $posiciones_por_fila = 5;
        foreach ($estantes as $estante_num => $filas) {
            foreach ($filas as $fila_num => $posiciones) {
                for ($i = 1; $i <= $posiciones_por_fila; $i++) {
                    if (!isset($estantes[$estante_num][$fila_num][$i])) {
                        $estantes[$estante_num][$fila_num][$i] = null;
                        $estantes_stats[$estante_num]['libres']++;
                    }
                }
            }
        }
        
        sort($estantes_disponibles);
        
        // Obtener productos disponibles para el modal
        $productos_disponibles = $this->modelo->obtenerTodos();
        
        $this->renderizar('views/estantes.php', [
            'estantes' => $estantes,
            'estantes_disponibles' => $estantes_disponibles,
            'estante_seleccionado' => $estante_seleccionado,
            'estantes_stats' => $estantes_stats,
            'productos_disponibles' => $productos_disponibles,
            'mensaje' => $this->mensaje,
            'tipo_mensaje' => $this->tipo_mensaje
        ]);
    }

    /**
     * Acción: Agregar producto a una posición específica del estante
     */
    public function agregar_a_posicion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }

        $producto_id = $_POST['producto_id'] ?? null;
        $cantidad = intval($_POST['cantidad'] ?? 0);
        $estante = intval($_POST['estante'] ?? 0);
        $fila = intval($_POST['fila'] ?? 0);
        $posicion = intval($_POST['posicion'] ?? 0);

        if (!$producto_id || !$cantidad || !$estante || !$fila) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'mensaje' => 'Datos incompletos']);
            exit;
        }

        // Aquí iría la lógica para actualizar el producto en esa posición
        // Por ahora, retornamos éxito
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'mensaje' => 'Producto agregado exitosamente']);
        exit;
    }

    /**
     * Validar datos del formulario
     */
    private function validar($codigo, $descripcion, $unidad, $cantidad, $estante, $entrepaño, $id = null) {
        $errores = [];

        if (empty($codigo)) {
            $errores[] = '✗ El código es requerido';
        }

        if (empty($descripcion)) {
            $errores[] = '✗ La descripción es requerida';
        }

        if (empty($unidad)) {
            $errores[] = '✗ La unidad es requerida';
        }

        if ($cantidad < 0) {
            $errores[] = '✗ La cantidad no puede ser negativa';
        }

        if ($estante <= 0) {
            $errores[] = '✗ Debe seleccionar un estante';
        }

        if ($entrepaño <= 0) {
            $errores[] = '✗ Debe seleccionar un entrepaño';
        }

        return $errores;
    }

    /**
     * Renderizar vista con datos
     */
    private function renderizar($vista, $datos = []) {
        extract($datos);
        require 'views/layout.php';
    }
}
?>