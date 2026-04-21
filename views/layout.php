<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inventario - EQUIMAC</title>
    <link rel="stylesheet" href="asset/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <h1>EQUIMAC</h1>
                <span class="subtitle">Sistema de Inventario</span>
            </div>
            <ul class="navbar-menu">
                <li><a href="index.php" class="active">Inventario</a></li>
                <li><a href="index.php?accion=formulario" class="btn-nuevo">+ Nuevo Producto</a></li>
            </ul>
        </div>
    </nav>

    <!-- Contenedor Principal -->
    <div class="container">
        <!-- Área de Mensajes -->
        <?php if (isset($mensaje) && $mensaje): ?>
            <div class="alerta alerta-<?php echo $tipo_mensaje; ?>">
                <span class="cerrar" onclick="this.parentElement.style.display='none';">&times;</span>
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Contenido -->
        <div class="contenido">
            <?php include $vista; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 EQUIMAC - Sistema de Inventario | Desarrollado con PHP + MySQL</p>
    </footer>

    <script src="asset/js/script.js"></script>
</body>
</html>
