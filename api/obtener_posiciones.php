<?php
/**
 * API: Obtener posiciones ocupadas
 * Archivo: api/obtener_posiciones.php
 * Responsabilidad: Retornar posiciones ocupadas en una fila del estante
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Inventario.php';

$estante = isset($_GET['estante']) ? intval($_GET['estante']) : null;
$entrepaño = isset($_GET['entrepaño']) ? intval($_GET['entrepaño']) : null;

if (!$estante || !$entrepaño) {
    echo json_encode(['error' => 'Parámetros inválidos']);
    exit;
}

$modelo = new Inventario();
$posiciones = $modelo->obtenerPosicionesOcupadas($estante, $entrepaño);

echo json_encode([
    'exito' => true,
    'posiciones' => $posiciones
]);
