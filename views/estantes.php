<!-- Vista: Visualización de Estantes Mejorada -->

<div class="seccion-estantes">
    <!-- Encabezado -->
    <div class="encabezado">
        <h2>🗂️ Visualización de Estantes</h2>
        <a href="index.php?accion=listar" class="btn btn-secondary">← Volver al Inventario</a>
    </div>

    <!-- Selector de Estante -->
    <div class="selector-estante">
        <label for="estante_selector">📍 Filtrar por Estante:</label>
        <select id="estante_selector" class="input-form" onchange="filtrarEstantePorNumero(this.value)">
            <option value="">-- Ver Todos los Estantes --</option>
            <?php foreach ($estantes_disponibles as $num_estante): ?>
                <option value="<?php echo $num_estante; ?>" <?php echo ($estante_seleccionado == $num_estante) ? 'selected' : ''; ?>>
                    Estante <?php echo $num_estante; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Contenedor de Estantes -->
    <div class="contenedor-estantes">
        <?php foreach ($estantes as $estante_num => $filas): ?>
            <div class="estante-grupo" data-estante="<?php echo $estante_num; ?>">
                <!-- Encabezado del Estante -->
                <div class="estante-titulo">
                    <h3>Estante <?php echo $estante_num; ?></h3>
                </div>
                
                <!-- Visualización del Estante -->
                <div class="estante-visual">
                    <?php 
                        $filasOrdenadas = array_keys($filas);
                        rsort($filasOrdenadas);
                    ?>
                    <?php foreach ($filasOrdenadas as $fila): ?>
                        <div class="fila-estante" data-fila="<?php echo $fila; ?>">
                            <div class="etiqueta-fila">Fila <?php echo $fila; ?></div>
                            
                            <div class="posiciones-contenedor">
                                <?php for ($pos = 1; $pos <= 5; $pos++): ?>
                                    <?php 
                                        $producto = isset($filas[$fila][$pos]) ? $filas[$fila][$pos] : null;
                                        $estaOcupada = $producto !== null;
                                    ?>
                                    <div class="posicion-estante" 
                                         data-estante="<?php echo $estante_num; ?>" 
                                         data-fila="<?php echo $fila; ?>" 
                                         data-posicion="<?php echo $pos; ?>"
                                         title="Estante <?php echo $estante_num; ?> - Fila <?php echo $fila; ?> - Posición <?php echo $pos; ?>">
                                        
                                        <?php if ($estaOcupada): ?>
                                            <!-- Posición Ocupada -->
                                            <div class="posicion-contenido ocupada" 
                                                 style="cursor: pointer;" 
                                                 onclick="abrirDetalleProducto(<?php echo $producto['id']; ?>)"
                                                 title="Clic para ver detalles">
                                                <div class="estado-color ocupado"></div>
                                                <div class="posicion-info">
                                                    <strong class="codigo-mini"><?php echo htmlspecialchars($producto['codigo']); ?></strong>
                                                    <div class="cantidad-pos">
                                                        <?php echo intval($producto['cantidad']); ?> 
                                                        <small><?php echo htmlspecialchars($producto['unidad']); ?></small>
                                                    </div>
                                                    <small class="desc-mini"><?php echo substr(htmlspecialchars($producto['descripcion']), 0, 18); ?>...</small>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <!-- Posición Libre -->
                                            <div class="posicion-contenido libre">
                                                <div class="estado-color libre"></div>
                                                <span class="label-libre">+</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Estadísticas del Estante -->
                <div class="estadisticas-estante">
                    <div class="stat-item">
                        <span class="stat-label">Filas</span>
                        <span class="stat-valor"><?php echo count($filas); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Ocupadas</span>
                        <span class="stat-valor ocupado-stat"><?php echo $estantes_stats[$estante_num]['ocupadas'] ?? 0; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Libres</span>
                        <span class="stat-valor libre-stat"><?php echo $estantes_stats[$estante_num]['libres'] ?? 0; ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Leyenda -->
    <div class="leyenda-estantes">
        <h4>📋 Leyenda:</h4>
        <div class="items-leyenda">
            <div class="item-leyenda">
                <div class="color-leyenda ocupado"></div>
                <span>Posición Ocupada - Haz clic para ver detalles</span>
            </div>
            <div class="item-leyenda">
                <div class="color-leyenda libre"></div>
                <span>Posición Libre</span>
            </div>
        </div>
    </div>
</div>

<script>
function filtrarEstantePorNumero(numeroEstante) {
    const estantes = document.querySelectorAll('.estante-grupo');

    if (numeroEstante === '') {
        // Mostrar todos
        estantes.forEach(estante => {
            estante.style.display = '';
        });
    } else {
        estantes.forEach(estante => {
            const numActual = estante.getAttribute('data-estante');
            if (numActual === numeroEstante) {
                estante.style.display = '';
            } else {
                estante.style.display = 'none';
            }
        });
    }
}

/**
 * Abre la página de detalles del producto en una nueva pestaña
 */
function abrirDetalleProducto(productoId) {
    window.open(`index.php?accion=detalles&id=${productoId}`, '_blank');
}
</script>