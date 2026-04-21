<!-- Vista: Listado de Productos -->

<div class="seccion-lista">
    <!-- Encabezado -->
    <div class="encabezado">
        <h2>Inventario de Productos</h2>
        <a href="index.php?accion=formulario" class="btn btn-primary">+ Agregar Producto</a>
    </div>

    <!-- Estadísticas -->
    <?php if ($estadisticas): ?>
    <div class="tarjetas-stats">
        <div class="tarjeta-stat">
            <h4>Total Productos</h4>
            <p class="numero"><?php echo $estadisticas['total_productos']; ?></p>
        </div>
        <div class="tarjeta-stat">
            <h4>Cantidad Total</h4>
            <p class="numero"><?php echo intval($estadisticas['cantidad_total']); ?> unidades</p>
        </div>
        <div class="tarjeta-stat">
            <h4>Valor Inventario</h4>
            <p class="numero">$<?php echo number_format($estadisticas['valor_total'], 2); ?></p>
        </div>
        <div class="tarjeta-stat">
            <h4>Precio Promedio</h4>
            <p class="numero">$<?php echo number_format($estadisticas['precio_promedio'], 2); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Buscador -->
    <div class="buscador">
        <form method="GET" class="formulario-busqueda">
            <input type="hidden" name="accion" value="listar">
            <div class="grupo-busqueda">
                <input type="text" name="buscar" placeholder="Buscar por código, nombre o categoría..." 
                       value="<?php echo htmlspecialchars($filtro ?? ''); ?>" class="input-busqueda">
                <button type="submit" class="btn btn-secondary">Buscar</button>
                <?php if ($filtro): ?>
                    <a href="index.php?accion=listar" class="btn btn-tercero">Limpiar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabla de Productos -->
    <div class="tabla-responsiva">
        <?php if (!empty($productos)): ?>
            <table class="tabla-inventario">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr class="fila-producto">
                            <td class="codigo">
                                <strong><?php echo htmlspecialchars($producto['codigo']); ?></strong>
                            </td>
                            <td class="nombre">
                                <div><?php echo htmlspecialchars($producto['nombre']); ?></div>
                                <?php if ($producto['descripcion']): ?>
                                    <small class="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-categoria">
                                    <?php echo htmlspecialchars($producto['categoria'] ?? 'Sin categoría'); ?>
                                </span>
                            </td>
                            <td class="cantidad">
                                <strong><?php echo intval($producto['cantidad']); ?></strong>
                            </td>
                            <td class="precio">
                                $<?php echo number_format($producto['precio_unitario'], 2); ?>
                            </td>
                            <td class="subtotal">
                                $<?php echo number_format($producto['cantidad'] * $producto['precio_unitario'], 2); ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $producto['estado']; ?>">
                                    <?php echo ucfirst($producto['estado']); ?>
                                </span>
                            </td>
                            <td class="acciones">
                                <a href="index.php?accion=formulario&id=<?php echo $producto['id']; ?>" 
                                   class="btn-accion btn-editar" title="Editar">
                                    ✎ Editar
                                </a>
                                <a href="index.php?accion=eliminar&id=<?php echo $producto['id']; ?>" 
                                   class="btn-accion btn-eliminar" 
                                   onclick="return confirmarEliminar('<?php echo htmlspecialchars($producto['nombre']); ?>');"
                                   title="Eliminar">
                                    🗑 Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="sin-resultados">
                <p>📦 No hay productos en el inventario</p>
                <a href="index.php?accion=formulario" class="btn btn-primary">Agregar primer producto</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Información -->
    <div class="info-footer">
        <p>Total de productos: <strong><?php echo count($productos); ?></strong></p>
        <p>Última actualización: <strong><?php echo date('d/m/Y H:i:s'); ?></strong></p>
    </div>
</div>
