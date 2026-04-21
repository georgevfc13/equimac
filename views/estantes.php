<!-- Vista: Visualización de Estantes -->

<div class="seccion-estantes">
    <!-- Encabezado -->
    <div class="encabezado">
        <h2>🗂️ Visualización de Estantes</h2>
        <a href="index.php?accion=listar" class="btn btn-secondary">← Volver al Inventario</a>
    </div>

    <!-- Selector de Estante -->
    <div class="selector-estante">
        <label for="estante_selector">Seleccionar Estante:</label>
        <select id="estante_selector" class="input-form">
            <option value="">-- Todos los Estantes --</option>
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
                <div class="estante-titulo">
                    <h3>Estante <?php echo $estante_num; ?></h3>
                </div>
                
                <div class="estante-visual">
                    <?php foreach ($filas as $fila_num => $posiciones): ?>
                        <div class="fila-estante" data-fila="<?php echo $fila_num; ?>">
                            <div class="etiqueta-fila">Fila <?php echo $fila_num; ?></div>
                            
                            <div class="posiciones-contenedor">
                                <?php foreach ($posiciones as $pos_num => $producto): ?>
                                    <div class="posicion-estante" 
                                         data-estante="<?php echo $estante_num; ?>" 
                                         data-fila="<?php echo $fila_num; ?>" 
                                         data-posicion="<?php echo $pos_num; ?>"
                                         title="Estante <?php echo $estante_num; ?> - Fila <?php echo $fila_num; ?> - Pos <?php echo $pos_num; ?>"
                                         style="cursor: pointer;">
                                        
                                        <?php if ($producto): ?>
                                            <!-- Posición Ocupada -->
                                            <div class="posicion-contenido ocupada">
                                                <div class="estado-color ocupado"></div>
                                                <div class="posicion-info">
                                                    <strong class="codigo-mini"><?php echo htmlspecialchars($producto['codigo']); ?></strong>
                                                    <div class="cantidad-pos">
                                                        <?php echo intval($producto['cantidad']); ?> 
                                                        <small><?php echo htmlspecialchars($producto['unidad']); ?></small>
                                                    </div>
                                                    <small class="desc-mini"><?php echo substr(htmlspecialchars($producto['descripcion']), 0, 20); ?>...</small>
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
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Estadísticas del Estante -->
                <div class="estadisticas-estante">
                    <div class="stat-item">
                        <span class="stat-label">Total Posiciones:</span>
                        <span class="stat-valor"><?php echo count($estantes_stats[$estante_num]['total_posiciones'] ?? []); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Ocupadas:</span>
                        <span class="stat-valor ocupado-stat"><?php echo $estantes_stats[$estante_num]['ocupadas'] ?? 0; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Libres:</span>
                        <span class="stat-valor libre-stat"><?php echo $estantes_stats[$estante_num]['libres'] ?? 0; ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal para agregar producto -->
    <div id="modal-agregar-posicion" class="modal">
        <div class="modal-contenido">
            <div class="modal-encabezado">
                <h2>Agregar/Cambiar Producto</h2>
                <span class="cerrar-modal">&times;</span>
            </div>
            <div class="modal-cuerpo">
                <div class="info-posicion">
                    <p><strong>Ubicación:</strong> <span id="posicion-info">Estante X - Fila Y - Posición Z</span></p>
                </div>
                
                <div class="grupo-form">
                    <label for="producto_selector">Seleccionar Producto:</label>
                    <select id="producto_selector" class="input-form">
                        <option value="">-- Seleccionar producto --</option>
                        <?php foreach ($productos_disponibles as $prod): ?>
                            <option value="<?php echo $prod['id']; ?>" data-codigo="<?php echo htmlspecialchars($prod['codigo']); ?>" data-desc="<?php echo htmlspecialchars($prod['descripcion']); ?>">
                                [<?php echo htmlspecialchars($prod['codigo']); ?>] <?php echo htmlspecialchars($prod['descripcion']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grupo-form">
                    <label for="cantidad_producto">Cantidad:</label>
                    <input type="number" id="cantidad_producto" class="input-form" min="1" value="1" placeholder="Cantidad">
                </div>

                <div class="grupo-acciones">
                    <button type="button" class="btn btn-primary" id="btn-confirmar-producto">Confirmar</button>
                    <button type="button" class="btn btn-secundario" id="btn-cancelar-producto">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Leyenda -->
    <div class="leyenda-estantes">
        <h4>Leyenda:</h4>
        <div class="items-leyenda">
            <div class="item-leyenda">
                <div class="color-leyenda ocupado"></div>
                <span>Posición Ocupada</span>
            </div>
            <div class="item-leyenda">
                <div class="color-leyenda libre"></div>
                <span>Posición Libre</span>
            </div>
        </div>
    </div>
</div>
