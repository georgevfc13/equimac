<?php
declare(strict_types=1);

// Local-only app bootstrap. No Composer required.
date_default_timezone_set('America/Mexico_City');

ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../app/Support/helpers.php';
require_once __DIR__ . '/../app/Support/Router.php';
require_once __DIR__ . '/../app/Support/Response.php';
require_once __DIR__ . '/../app/Support/Database.php';

require_once __DIR__ . '/../app/Models/Estantes.php';
require_once __DIR__ . '/../app/Models/Inventario.php';
require_once __DIR__ . '/../app/Models/Salidas.php';

require_once __DIR__ . '/../app/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Controllers/InventarioController.php';
require_once __DIR__ . '/../app/Controllers/EstantesController.php';
require_once __DIR__ . '/../app/Controllers/SalidaController.php';
require_once __DIR__ . '/../app/Controllers/ApiController.php';

use App\Support\Router;
use App\Support\Response;

try {
    $router = new Router();

    // Pages
    $router->get('/', [App\Controllers\HomeController::class, 'redirectToInventario']);
    $router->get('/inventario', [App\Controllers\InventarioController::class, 'index']);
    $router->get('/inventario/nuevo', [App\Controllers\InventarioController::class, 'create']);
    $router->get('/inventario/{id}', [App\Controllers\InventarioController::class, 'show']);
    $router->get('/inventario/{id}/editar', [App\Controllers\InventarioController::class, 'edit']);

    // Actions (POST)
    $router->post('/inventario/guardar', [App\Controllers\InventarioController::class, 'storeOrUpdate']);
    $router->post('/inventario/{id}/eliminar', [App\Controllers\InventarioController::class, 'destroy']);

    $router->get('/estantes', [App\Controllers\EstantesController::class, 'index']);
    $router->post('/estantes/guardar', [App\Controllers\EstantesController::class, 'store']);
    $router->post('/estantes/{id}/eliminar', [App\Controllers\EstantesController::class, 'destroy']);

    $router->get('/salida', [App\Controllers\SalidaController::class, 'form']);
    $router->post('/salida/guardar', [App\Controllers\SalidaController::class, 'store']);

    // API (JSON)
    $router->get('/api/estante/{estante}/posiciones', [App\Controllers\ApiController::class, 'posicionesEstante']);
    $router->get('/api/posiciones-ocupadas', [App\Controllers\ApiController::class, 'posicionesOcupadas']);
    $router->get('/api/inventario/buscar', [App\Controllers\ApiController::class, 'buscarInventario']);
    $router->post('/api/inventario/eliminar', [App\Controllers\ApiController::class, 'eliminarInventario']);

    $router->dispatch();
} catch (Throwable $e) {
    Response::html(view('error/500', [
        'title' => 'Error inesperado',
        'message' => $e->getMessage(),
    ]), 500)->send();
}

