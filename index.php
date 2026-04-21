<?php
/**
 * ============================================
 * SISTEMA DE INVENTARIO - EQUIMAC
 * Arquitectura MVC
 * ============================================
 * Archivo: index.php
 * Responsabilidad: Punto de entrada y enrutador
 */

// ============================================
// CONFIGURACIÓN INICIAL
// ============================================

// Configurar zona horaria
date_default_timezone_set('America/Mexico_City');

// Habilitar reporte de errores en desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 0);  // Cambiar a 0 en producción

// Incluir configuración de BD
require_once __DIR__ . '/config/database.php';

// Incluir controlador
require_once __DIR__ . '/controllers/InventarioController.php';

// ============================================
// ENRUTADOR MVC
// ============================================

// Obtener la acción solicitada
$accion = $_GET['accion'] ?? 'listar';

// Crear instancia del controlador
$controlador = new InventarioController();

// Enrutar a la acción correspondiente
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

        default:
            // Acción por defecto
            $controlador->listar();
            break;
    }
} catch (Exception $e) {
    // Manejo de errores
    $mensaje = "Error: " . $e->getMessage();
    $tipo_mensaje = 'error';
    require __DIR__ . '/views/layout.php';
}

// ============================================
// NOTA SOBRE LA ARQUITECTURA
// ============================================
/*
 * FLUJO MVC:
 * 
 * 1. USUARIO hace solicitud (GET/POST)
 * 2. INDEX.PHP (enrutador) identifica acción
 * 3. CONTROLADOR procesa la lógica
 *    - Valida datos
 *    - Interactúa con modelo
 *    - Prepara datos para vista
 * 4. MODELO accede a la BD
 *    - Ejecuta queries
 *    - Retorna datos
 * 5. VISTA renderiza la interfaz
 *    - Muestra datos
 *    - Genera HTML/CSS
 * 6. RESPUESTA se envía al navegador
 */
?>
