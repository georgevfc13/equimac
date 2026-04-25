<?php
date_default_timezone_set('America/Mexico_City');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/InventarioController.php';

$accion = $_GET['accion'] ?? 'listar';
$controlador = new InventarioController();

try {
    switch ($accion) {
        case 'listar':
            $controlador->listar();
            break;
        case 'formulario':
            $controlador->formulario();
            break;
        case 'guardar':
            $controlador->guardar();
            break;
        case 'eliminar':
            $controlador->eliminar();
            break;
        case 'estantes':
            $controlador->estantes();
            break;
        case 'agregar_a_posicion':
            $controlador->agregar_a_posicion();
            break;
        default:
            $controlador->listar();
            break;
    }
} catch (Exception $e) {
    $mensaje = "Error: " . $e->getMessage();
    $tipo_mensaje = 'error';
    require __DIR__ . '/views/layout.php';
}
?>
