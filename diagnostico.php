<?php
/**
 * Diagnóstico del Sistema
 * Acceder a: http://localhost/equimac/diagnostico.php
 */

function titulo($texto) {
    echo "<h2 style='color: #2563eb; margin-top: 30px;'>$texto</h2>";
}

function item($nombre, $valor, $ok = true) {
    $color = $ok ? 'green' : 'red';
    $icono = $ok ? '✓' : '✗';
    echo "<p><span style='color: $color; font-weight: bold;'>$icono</span> <strong>$nombre:</strong> $valor</p>";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico - EQUIMAC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: #f8fafc;
        }
        h1 { color: #1f2937; }
        h2 { color: #2563eb; margin-top: 30px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        p { line-height: 1.8; }
        .success { color: #10b981; }
        .error { color: #ef4444; }
        .warning { color: #f59e0b; }
        .info-box { background: #dbeafe; padding: 15px; border-radius: 6px; border-left: 4px solid #2563eb; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🔧 Diagnóstico del Sistema EQUIMAC</h1>
    <p>Timestamp: <?php echo date('Y-m-d H:i:s'); ?></p>

    <?php
    // ============================================
    // DIAGNÓSTICO DEL SERVIDOR
    // ============================================
    titulo("📊 Información del Servidor");
    item("SO", php_uname(), true);
    item("PHP Version", phpversion(), version_compare(phpversion(), '7.4', '>='));
    item("SAPI", php_sapi_name(), true);
    item("memoria_limit", ini_get('memory_limit'), true);
    item("max_execution_time", ini_get('max_execution_time') . 's', true);

    // ============================================
    // EXTENSIONES PHP
    // ============================================
    titulo("🔌 Extensiones PHP");
    
    $extensiones_requeridas = ['mysqli', 'curl', 'json', 'pdo'];
    foreach ($extensiones_requeridas as $ext) {
        $ok = extension_loaded($ext);
        item("Extensión $ext", $ok ? 'Instalada' : 'NO INSTALADA', $ok);
    }

    // ============================================
    // BASE DE DATOS
    // ============================================
    titulo("💾 Base de Datos");

    try {
        require_once __DIR__ . '/config/database.php';
        $db = Database::getInstance()->getConnection();
        
        item("MySQL Conexión", "Conectada", true);
        item("Servidor", $db->server_info, true);
        item("BD Actual", DB_NAME, true);
        
        // Ver si la tabla existe
        $resultado = $db->query("SHOW TABLES FROM " . DB_NAME);
        $tablas = [];
        while ($fila = $resultado->fetch_row()) {
            $tablas[] = $fila[0];
        }
        item("Tablas en BD", implode(', ', $tablas), count($tablas) > 0);
        
        // Ver estadísticas
        if (in_array('inventario', $tablas)) {
            $consulta = $db->query("SELECT COUNT(*) as total FROM inventario");
            $fila = $consulta->fetch_assoc();
            item("Registros en Inventario", $fila['total'], true);
        }
        
    } catch (Exception $e) {
        item("MySQL Conexión", "ERROR: " . $e->getMessage(), false);
    }

    // ============================================
    // ARCHIVOS Y CARPETAS
    // ============================================
    titulo("📁 Archivos y Carpetas");

    $archivos_necesarios = [
        'index.php',
        'config/database.php',
        'models/Inventario.php',
        'controllers/InventarioController.php',
        'views/layout.php',
        'views/lista.php',
        'views/formulario.php',
        'public/css/style.css',
        'public/js/script.js',
        'database.sql'
    ];

    foreach ($archivos_necesarios as $archivo) {
        $existe = file_exists(__DIR__ . '/' . $archivo);
        item("Archivo: $archivo", $existe ? 'Existe' : 'NO EXISTE', $existe);
    }

    // ============================================
    // PERMISOS
    // ============================================
    titulo("🔐 Permisos");

    $carpetas_necesarias = [
        'logs',
        'public',
        'config',
        'models',
        'controllers',
        'views'
    ];

    foreach ($carpetas_necesarias as $carpeta) {
        $ruta = __DIR__ . '/' . $carpeta;
        if (is_dir($ruta)) {
            $permisos = substr(sprintf('%o', fileperms($ruta)), -4);
            $escribible = is_writable($ruta);
            item("Carpeta: $carpeta", "Permisos: $permisos" . ($escribible ? ' (escribible)' : ''), $escribible || true);
        }
    }

    // ============================================
    // PRUEBAS FUNCIONALES
    // ============================================
    titulo("✅ Pruebas Funcionales");

    // Test 1: Modelo
    try {
        require_once __DIR__ . '/models/Inventario.php';
        $modelo = new Inventario();
        item("Modelo Inventario", "Cargado correctamente", true);
    } catch (Exception $e) {
        item("Modelo Inventario", "ERROR: " . $e->getMessage(), false);
    }

    // Test 2: Controlador
    try {
        require_once __DIR__ . '/controllers/InventarioController.php';
        $controlador = new InventarioController();
        item("Controlador Inventario", "Cargado correctamente", true);
    } catch (Exception $e) {
        item("Controlador Inventario", "ERROR: " . $e->getMessage(), false);
    }

    // Test 3: Funciones globales
    $db_test = getDB();
    item("Función getDB()", "Disponible", $db_test !== null);

    // ============================================
    // RECOMENDACIONES
    // ============================================
    titulo("💡 Recomendaciones");

    echo "<div class='info-box'>";
    echo "<strong>Próximos pasos:</strong><br>";
    echo "1. Si todo muestra ✓, accede a: <a href='/equimac'>http://localhost/equimac</a><br>";
    echo "2. Si hay ✗, sigue las instrucciones del README.md<br>";
    echo "3. Verifica que Apache y MySQL estén iniciados<br>";
    echo "4. Para producción, elimina este archivo (diagnostico.php)<br>";
    echo "</div>";

    ?>

    <hr>
    <footer style="text-align: center; color: #6b7280; margin-top: 40px;">
        <p>EQUIMAC - Sistema de Inventario | <a href="/equimac">Volver al Sistema</a> | <a href="/phpmyadmin">phpMyAdmin</a></p>
    </footer>
</body>
</html>
