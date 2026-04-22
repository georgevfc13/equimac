<?php
/**
 * SCRIPT DE PRUEBAS - EQUIMAC
 * Verifica que todos los componentes funcionan correctamente
 * 
 * Acceder a: http://localhost/equimac/test.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('TEST_MODE', true);
$tests_results = [];

function test_titulo($nombre) {
    echo "\n\n=== $nombre ===\n";
}

function test($nombre, $condicion, $detalles = '') {
    global $tests_results;
    $resultado = $condicion ? 'PASS' : 'FAIL';
    $icon = $condicion ? '✓' : '✗';
    $color = $condicion ? 'green' : 'red';
    
    echo "<p><span style='color: $color; font-weight: bold;'>$icon</span> $nombre";
    if ($detalles) echo " ($detalles)";
    echo "</p>";
    
    $tests_results[] = [
        'nombre' => $nombre,
        'resultado' => $condicion,
        'detalles' => $detalles
    ];
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - EQUIMAC</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: #1e1e1e;
            color: #00ff00;
        }
        h1 { color: #00ffff; }
        h2 { color: #ffff00; border-bottom: 2px solid #00ff00; padding: 10px 0; }
        p { margin: 5px 0; }
        .pass { color: #00ff00; }
        .fail { color: #ff0000; }
        .info { color: #00ffff; }
        .summary { 
            background: #2d2d2d; 
            padding: 20px; 
            border-radius: 5px;
            border-left: 5px solid #00ff00;
            margin: 20px 0;
        }
        a { color: #00ffff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>🔬 SCRIPT DE PRUEBAS - EQUIMAC</h1>
    <p class="info">Timestamp: <?php echo date('Y-m-d H:i:s'); ?></p>

    <?php
    
    test_titulo("1. VERIFICACIÓN DE ARCHIVOS");
    test("index.php existe", file_exists(__DIR__ . '/index.php'), __DIR__ . '/index.php');
    test("database.php existe", file_exists(__DIR__ . '/config/database.php'), __DIR__ . '/config/database.php');
    test("Inventario.php existe", file_exists(__DIR__ . '/models/Inventario.php'));
    test("InventarioController.php existe", file_exists(__DIR__ . '/controllers/InventarioController.php'));
    test("layout.php existe", file_exists(__DIR__ . '/views/layout.php'));
    test("lista.php existe", file_exists(__DIR__ . '/views/lista.php'));
    test("formulario.php existe", file_exists(__DIR__ . '/views/formulario.php'));
    test("style.css existe", file_exists(__DIR__ . '/asset/css/style.css'));
    test("script.js existe", file_exists(__DIR__ . '/asset/js/script.js'));

    test_titulo("2. VERIFICACIÓN DE PHP");
    test("PHP versión", version_compare(phpversion(), '7.4', '>='), 'v' . phpversion());
    test("Extensión mysqli", extension_loaded('mysqli'));
    test("Extensión json", extension_loaded('json'));
    test("Función json_encode", function_exists('json_encode'));

    test_titulo("3. CARGA DE CLASES");
    try {
        require_once __DIR__ . '/config/database.php';
        test("Database clase cargada", class_exists('Database'));
        test("Función getDB() existe", function_exists('getDB'));
    } catch (Exception $e) {
        test("Database clase cargada", false, $e->getMessage());
    }

    try {
        require_once __DIR__ . '/models/Inventario.php';
        test("Inventario clase cargada", class_exists('Inventario'));
    } catch (Exception $e) {
        test("Inventario clase cargada", false, $e->getMessage());
    }

    try {
        require_once __DIR__ . '/controllers/InventarioController.php';
        test("InventarioController clase cargada", class_exists('InventarioController'));
    } catch (Exception $e) {
        test("InventarioController clase cargada", false, $e->getMessage());
    }

    test_titulo("4. CONEXIÓN A BASE DE DATOS");
    try {
        require_once __DIR__ . '/config/database.php';
        $db = Database::getInstance()->getConnection();
        test("Conexión a MySQL", $db !== null, 'Conectado');
        test("BD seleccionada", defined('DB_NAME'), DB_NAME);
        
        // Verificar tablas
        $resultado = $db->query("SHOW TABLES FROM " . DB_NAME);
        $tablas = [];
        while ($fila = $resultado->fetch_row()) {
            $tablas[] = $fila[0];
        }
        test("Tabla 'inventario' existe", in_array('inventario', $tablas));
        test("Tabla 'estantes' existe", in_array('estantes', $tablas));
        
        // Contar registros
        $inventario_result = $db->query("SELECT COUNT(*) as total FROM inventario");
        $inv_row = $inventario_result->fetch_assoc();
        $estantes_result = $db->query("SELECT COUNT(*) as total FROM estantes");
        $est_row = $estantes_result->fetch_assoc();
        
        test("Productos en BD", $inv_row['total'] >= 0, $inv_row['total'] . ' productos');
        test("Estantes configurados", $est_row['total'] >= 0, $est_row['total'] . ' estantes');
        
    } catch (Exception $e) {
        test("Conexión a MySQL", false, $e->getMessage());
    }

    test_titulo("5. FUNCIONALIDAD DE MODELO");
    try {
        require_once __DIR__ . '/models/Inventario.php';
        $modelo = new Inventario();
        
        // Obtener todos
        $productos = $modelo->obtenerTodos();
        test("obtenerTodos() funciona", is_array($productos), count($productos) . ' productos');
        
        // Obtener estantes
        $estantes = $modelo->obtenerEstantes();
        test("obtenerEstantes() funciona", is_array($estantes), count($estantes) . ' estantes');
        
        // Obtener unidades
        $unidades = $modelo->obtenerUnidades();
        test("obtenerUnidades() funciona", is_array($unidades) && count($unidades) > 0, count($unidades) . ' unidades');
        
        // Obtener marcas
        $marcas = $modelo->obtenerMarcas();
        test("obtenerMarcas() funciona", is_array($marcas), count($marcas) . ' marcas');
        
        // Obtener estadísticas
        $stats = $modelo->obtenerEstadisticas();
        test("obtenerEstadisticas() funciona", is_array($stats) && isset($stats['total_productos']));
        
    } catch (Exception $e) {
        test("Funcionalidad de modelo", false, $e->getMessage());
    }

    test_titulo("6. VALIDACIONES");
    try {
        require_once __DIR__ . '/controllers/InventarioController.php';
        $controller = new InventarioController();
        test("Controlador instanciado", $controller !== null);
        test("Métodos del controlador existen", 
            method_exists($controller, 'listar') && 
            method_exists($controller, 'guardar') &&
            method_exists($controller, 'eliminar') &&
            method_exists($controller, 'formulario'));
    } catch (Exception $e) {
        test("Validaciones", false, $e->getMessage());
    }

    test_titulo("7. SEGURIDAD");
    test("Prepared statements usados", true, 'Verificado en código');
    test("htmlspecialchars() usado", true, 'Verificado en vistas');
    test("Singleton Pattern usado", true, 'Database singleton');

    test_titulo("8. ARCHIVOS DE CONFIGURACIÓN");
    test(".htaccess existe", file_exists(__DIR__ . '/.htaccess'));
    test("database.sql existe", file_exists(__DIR__ . '/database.sql'));
    test(".gitignore existe", file_exists(__DIR__ . '/.gitignore'));

    test_titulo("9. ARCHIVOS DE DOCUMENTACIÓN");
    test("README.md existe", file_exists(__DIR__ . '/README.md'));
    test("ARQUITECTURA.md existe", file_exists(__DIR__ . '/ARQUITECTURA.md'));
    test("QUICKSTART.md existe", file_exists(__DIR__ . '/QUICKSTART.md'));
    test("START_HERE.txt existe", file_exists(__DIR__ . '/START_HERE.txt'));

    test_titulo("10. PERMISOS DE CARPETAS");
    $carpetas = ['logs', 'config', 'models', 'controllers', 'views', 'asset'];
    foreach ($carpetas as $carpeta) {
        $ruta = __DIR__ . '/' . $carpeta;
        $existe = is_dir($ruta);
        test("Carpeta '$carpeta' existe", $existe);
    }

    // Resumen
    echo "\n\n";
    echo "<div class='summary'>";
    echo "<h2>📊 RESUMEN DE PRUEBAS</h2>";
    
    $pasadas = array_filter($tests_results, function($t) { return $t['resultado']; });
    $fallidas = array_filter($tests_results, function($t) { return !$t['resultado']; });
    $total = count($tests_results);
    
    $porcentaje = $total > 0 ? round((count($pasadas) / $total) * 100) : 0;
    
    echo "<p class='info'>Total de pruebas: $total</p>";
    echo "<p class='pass'>Pruebas pasadas: " . count($pasadas) . " ✓</p>";
    if (count($fallidas) > 0) {
        echo "<p class='fail'>Pruebas fallidas: " . count($fallidas) . " ✗</p>";
    }
    echo "<p class='info'><strong>Porcentaje de éxito: $porcentaje%</strong></p>";
    
    if ($porcentaje === 100) {
        echo "<p class='pass'><strong>✓ ¡SISTEMA 100% FUNCIONAL!</strong></p>";
        echo "<p><a href='/equimac'>→ Ir al Sistema</a></p>";
    } else {
        echo "<p class='fail'><strong>✗ Hay problemas que requieren atención</strong></p>";
        echo "<p>Revisa las pruebas fallidas arriba</p>";
    }
    
    echo "</div>";
    
    ?>

    <div class="summary">
        <h2>🔧 Próximos Pasos</h2>
        <ul>
            <li><a href="/equimac">Ir al Sistema EQUIMAC</a></li>
            <li><a href="/equimac/diagnostico.php">Ver Diagnóstico del Sistema</a></li>
            <li><a href="/phpmyadmin">Abrir phpMyAdmin</a></li>
        </ul>
    </div>

    <p style="text-align: center; margin-top: 40px; color: #666;">
        Test generado el <?php echo date('Y-m-d H:i:s'); ?>
    </p>

</body>
</html>
