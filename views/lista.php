<!-- Vista: Listado de Productos -->

<div class="seccion-lista">
    <!-- Encabezado -->
    <div class="encabezado">
        <h2>📦 Inventario de Productos</h2>
        <a href="index.php?accion=formulario" class="btn btn-primary">+ Agregar Producto</a>
    </div>

    <!-- Estadísticas -->
    <?php if ($estadisticas): ?>
    <div class="tarjetas-stats">
        <div class="tarjeta-stat">
            <h4>📊 Total Productos</h4>
            <p class="numero"><?php echo $estadisticas['total_productos']; ?></p>
        </div>
        <div class="tarjeta-stat">
            <h4>📦 Cantidad Total</h4>
            <p class="numero"><?php echo intval($estadisticas['cantidad_total']); ?> unidades</p>
        </div>
        <div class="tarjeta-stat">
            <h4>🗂️ Estantes Usados</h4>
            <p class="numero"><?php echo $estadisticas['total_estantes']; ?></p>
        </div>
        <div class="tarjeta-stat">
            <h4>🏷️ Marcas</h4>
            <p class="numero"><?php echo $estadisticas['total_marcas']; ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Buscador -->
    <div class="buscador">
        <form method="GET" class="formulario-busqueda">
            <input type="hidden" name="accion" value="listar">
            <div class="grupo-busqueda">
                <input type="text" name="buscar" placeholder="Buscar por código, descripción, marca o equipo..." 
                       value="<?php echo htmlspecialchars($filtro ?? ''); ?>" class="input-busqueda">
                <button type="submit" class="btn btn-secondary">🔍 Buscar</button>
                <?php if ($filtro): ?>
                    <a href="index.php?accion=listar" class="btn btn-tercero">✕ Limpiar</a>
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
                        <th>Descripción</th>
                        <th>Marca</th>
                        <th>Unidad</th>
                        <th>Cantidad</th>
                        <th>Ubicación</th>
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
                            <td class="descripcion">
                                <div><?php echo htmlspecialchars($producto['descripcion']); ?></div>
                                <?php if ($producto['equipo']): ?>
                                    <small style="color: #666;">📌 <?php echo htmlspecialchars($producto['equipo']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($producto['marca']): ?>
                                    <span class="badge badge-marca">
                                        <?php echo htmlspecialchars($producto['marca']); ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #999;">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="unidad">
                                <small><?php echo htmlspecialchars($producto['unidad']); ?></small>
                            </td>
                            <td class="cantidad">
                                <strong><?php echo intval($producto['cantidad']); ?></strong>
                            </td>
                            <td class="ubicacion">
                                <span class="badge badge-ubicacion">
                                    📍 Est. <?php echo $producto['estante']; ?> - F. <?php echo $producto['entrepaño']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($producto['estado']): ?>
                                    <span class="badge badge-<?php echo strtolower(str_replace(' ', '-', $producto['estado'])); ?>">
                                        <?php echo htmlspecialchars($producto['estado']); ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #999;">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="acciones">
                                <a href="index.php?accion=detalles&id=<?php echo $producto['id']; ?>" 
                                   class="btn-accion btn-info" title="Ver detalles">
                                    👁 Detalles
                                </a>
                                <a href="index.php?accion=formulario&id=<?php echo $producto['id']; ?>" 
                                   class="btn-accion btn-editar" title="Editar">
                                    ✎ Editar
                                </a>
                                <a href="index.php?accion=eliminar&id=<?php echo $producto['id']; ?>" 
                                   class="btn-accion btn-eliminar" 
                                   onclick="return confirmarEliminar('<?php echo htmlspecialchars($producto['descripcion']); ?>');"
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