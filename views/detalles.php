<!-- Vista: Detalles Completos del Producto -->

<div class="seccion-detalles">
    <!-- Encabezado -->
    <div class="encabezado-detalles">
        <h2>📋 Detalles del Producto</h2>
        <div class="botones-encabezado">
            <a href="index.php?accion=formulario&id=<?php echo $producto['id']; ?>" class="btn btn-primary">✎ Editar</a>
            <a href="index.php?accion=listar" class="btn btn-secundario">← Volver</a>
        </div>
    </div>

    <div class="contenido-detalles">
        <!-- Columna Izquierda: Información del Producto -->
        <div class="columna-info">
            <!-- Información Básica -->
            <div class="tarjeta-seccion">
                <h3 class="titulo-seccion">📌 Información Básica</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Código:</label>
                        <p class="valor codigo-destacado"><?php echo htmlspecialchars($producto['codigo']); ?></p>
                    </div>
                    <div class="info-item">
                        <label>Descripción:</label>
                        <p class="valor"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    </div>
                    <div class="info-item">
                        <label>Unidad de Medida:</label>
                        <p class="valor"><?php echo htmlspecialchars($producto['unidad']); ?></p>
                    </div>
                    <div class="info-item">
                        <label>Cantidad:</label>
                        <p class="valor cantidad-grande"><?php echo intval($producto['cantidad']); ?> unidades</p>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="tarjeta-seccion">
                <h3 class="titulo-seccion">🏷️ Información Adicional</h3>
                <div class="info-grid">
                    <?php if ($producto['marca']): ?>
                    <div class="info-item">
                        <label>Marca:</label>
                        <p class="valor badge badge-marca"><?php echo htmlspecialchars($producto['marca']); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($producto['equipo']): ?>
                    <div class="info-item">
                        <label>Equipo:</label>
                        <p class="valor"><?php echo htmlspecialchars($producto['equipo']); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($producto['aplicacion']): ?>
                    <div class="info-item">
                        <label>Aplicación:</label>
                        <p class="valor"><?php echo htmlspecialchars($producto['aplicacion']); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($producto['tipo_maquinaria']): ?>
                    <div class="info-item">
                        <label>Tipo de Maquinaria:</label>
                        <p class="valor"><?php echo htmlspecialchars($producto['tipo_maquinaria']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Estado -->
            <div class="tarjeta-seccion">
                <h3 class="titulo-seccion">⚙️ Estado</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Estado:</label>
                        <p class="valor">
                            <?php if ($producto['estado']): ?>
                                <span class="badge badge-<?php echo strtolower(str_replace(' ', '-', $producto['estado'])); ?>">
                                    <?php echo htmlspecialchars($producto['estado']); ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #999;">Sin estado</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información de Fechas -->
            <div class="tarjeta-seccion">
                <h3 class="titulo-seccion">📅 Información de Sistema</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Creado:</label>
                        <p class="valor" style="font-size: 12px;">
                            <?php echo date('d/m/Y H:i', strtotime($producto['fecha_creacion'])); ?>
                        </p>
                    </div>
                    <div class="info-item">
                        <label>Actualizado:</label>
                        <p class="valor" style="font-size: 12px;">
                            <?php echo date('d/m/Y H:i', strtotime($producto['fecha_actualizacion'])); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Ubicación en Estante -->
        <div class="columna-estante">
            <!-- Ubicación -->
            <div class="tarjeta-seccion tarjeta-ubicacion">
                <h3 class="titulo-seccion">📍 Ubicación en Estante</h3>
                
                <?php if ($estante): ?>
                    <div class="info-ubicacion">
                        <div class="info-item">
                            <label>Estante:</label>
                            <p class="valor estante-numero"><?php echo $estante['numero']; ?></p>
                        </div>
                        <div class="info-item">
                            <label>Descripción:</label>
                            <p class="valor"><?php echo htmlspecialchars($estante['descripcion']); ?></p>
                        </div>
                        <div class="info-item">
                            <label>Ubicación Física:</label>
                            <p class="valor"><?php echo htmlspecialchars($estante['ubicacion']); ?></p>
                        </div>
                        <div class="info-item">
                            <label>Fila/Entrepaño:</label>
                            <p class="valor fila-entrepaño">Fila <strong><?php echo $producto['entrepaño']; ?></strong></p>
                        </div>
                    </div>
                <?php else: ?>
                    <p>No hay información de estante disponible</p>
                <?php endif; ?>
            </div>

            <!-- Esquema Visual del Estante -->
            <div class="tarjeta-seccion tarjeta-esquema">
                <h3 class="titulo-seccion">🗂️ Esquema del Estante <?php echo $estante['numero']; ?></h3>
                
                <div class="leyenda-estante">
                    <div class="leyenda-item">
                        <div class="cuadro-leyenda ubicacion-actual"></div>
                        <span>Ubicación actual del producto</span>
                    </div>
                    <div class="leyenda-item">
                        <div class="cuadro-leyenda otros-productos"></div>
                        <span>Otros productos en esta fila</span>
                    </div>
                    <div class="leyenda-item">
                        <div class="cuadro-leyenda vacio"></div>
                        <span>Espacio vacío</span>
                    </div>
                </div>

                <div class="contenedor-esquema">
                    <?php if ($estante): ?>
                        <?php 
                            // Obtener productos en cada ubicación del estante
                            $mapaMalla = [];
                            for ($fila = 1; $fila <= $estante['filas']; $fila++) {
                                for ($col = 1; $col <= $estante['columnas']; $col++) {
                                    $mapaMalla[$fila][$col] = null;
                                }
                            }
                            
                            // Llenar productos en la misma fila
                            foreach ($productosEnUbicacion as $prod) {
                                $mapaMalla[$prod['entrepaño']][1] = $prod; // Simplificar a una columna por fila para este ejemplo
                            }
                        ?>
                        
                        <div class="esquema-estante">
                            <!-- Numeración de columnas -->
                            <div class="fila-numeracion">
                                <div class="label-fila"></div>
                                <?php for ($col = 1; $col <= $estante['columnas']; $col++): ?>
                                    <div class="numero-columna">C<?php echo $col; ?></div>
                                <?php endfor; ?>
                            </div>

                            <!-- Filas del estante -->
                            <?php for ($fila = $estante['filas']; $fila >= 1; $fila--): ?>
                                <div class="fila-estante">
                                    <div class="label-fila">Fila <?php echo $fila; ?></div>
                                    
                                    <?php for ($col = 1; $col <= $estante['columnas']; $col++): ?>
                                        <?php 
                                            $esUbicacionActual = ($fila == $producto['entrepaño']);
                                            $contieneProducto = isset($mapaMalla[$fila][$col]) && $mapaMalla[$fila][$col] !== null;
                                        ?>
                                        
                                        <div class="celda-estante 
                                            <?php echo $esUbicacionActual ? 'ubicacion-actual' : ''; ?>
                                            <?php echo ($contieneProducto && !$esUbicacionActual) ? 'otros-productos' : ''; ?>
                                            <?php echo (!$contieneProducto && !$esUbicacionActual) ? 'vacio' : ''; ?>"
                                            title="<?php echo $esUbicacionActual ? 'UBICACIÓN ACTUAL: ' . htmlspecialchars($producto['codigo']) : ''; ?>">
                                            
                                            <?php if ($esUbicacionActual): ?>
                                                <strong>✓</strong>
                                                <span class="codigo-celda"><?php echo htmlspecialchars($producto['codigo']); ?></span>
                                            <?php elseif ($contieneProducto): ?>
                                                <span class="codigo-celda">●</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <!-- Información de productos en la misma fila -->
                        <?php if (count($productosEnUbicacion) > 1): ?>
                            <div class="productos-fila" style="margin-top: 20px;">
                                <h4>📦 Otros productos en esta fila (Fila <?php echo $producto['entrepaño']; ?>):</h4>
                                <div class="lista-productos-fila">
                                    <?php foreach ($productosEnUbicacion as $prod): ?>
                                        <?php if ($prod['id'] != $producto['id']): ?>
                                            <div class="item-producto-fila">
                                                <span class="codigo-fila"><?php echo htmlspecialchars($prod['codigo']); ?></span>
                                                <span class="descripcion-fila"><?php echo htmlspecialchars($prod['descripcion']); ?></span>
                                                <span class="cantidad-fila">(<?php echo $prod['cantidad']; ?> unidades)</span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <p>No hay información de estante disponible</p>
                    <?php endif; ?>
                </div>

                <!-- Botón para imprimir -->
                <div style="margin-top: 15px; text-align: center;">
                    <button type="button" class="btn btn-secondary" onclick="window.print();">
                        🖨️ Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .seccion-detalles {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .encabezado-detalles {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }

    .encabezado-detalles h2 {
        margin: 0;
        color: #333;
    }

    .botones-encabezado {
        display: flex;
        gap: 10px;
    }

    .contenido-detalles {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }

    @media (max-width: 1200px) {
        .contenido-detalles {
            grid-template-columns: 1fr;
        }
    }

    .tarjeta-seccion {
        background: #f9f9f9;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .tarjeta-ubicacion {
        background: #f0f8ff;
        border-color: #b3d9ff;
    }

    .tarjeta-esquema {
        background: #fff9e6;
        border-color: #ffe680;
        grid-column: 1 / -1;
    }

    .titulo-seccion {
        margin: 0 0 15px 0;
        color: #333;
        font-size: 16px;
        border-bottom: 2px solid #ddd;
        padding-bottom: 8px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .info-item {
        margin-bottom: 10px;
    }

    .info-item label {
        display: block;
        font-weight: bold;
        color: #666;
        font-size: 12px;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .info-item .valor {
        margin: 0;
        color: #333;
        font-size: 14px;
        word-break: break-word;
    }

    .codigo-destacado {
        font-size: 18px !important;
        font-weight: bold;
        color: #2196F3;
        background: #f0f7ff;
        padding: 8px 12px;
        border-radius: 4px;
        display: inline-block;
    }

    .cantidad-grande {
        font-size: 20px !important;
        font-weight: bold;
        color: #4CAF50;
    }

    .estante-numero {
        font-size: 24px !important;
        font-weight: bold;
        color: #FF9800;
        background: #fff3e0;
        padding: 10px 15px;
        border-radius: 4px;
        display: inline-block;
        text-align: center;
        width: 100%;
    }

    .fila-entrepaño {
        font-size: 16px !important;
        color: #E91E63;
        background: #ffe0e6;
        padding: 8px 12px;
        border-radius: 4px;
        font-weight: bold;
    }

    /* Estilos del esquema */
    .leyenda-estante {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
        padding: 10px;
        background: white;
        border-radius: 4px;
        font-size: 13px;
    }

    .leyenda-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cuadro-leyenda {
        width: 20px;
        height: 20px;
        border: 1px solid #999;
        border-radius: 3px;
    }

    .cuadro-leyenda.ubicacion-actual {
        background: #4CAF50;
        border-color: #2e7d32;
    }

    .cuadro-leyenda.otros-productos {
        background: #FFB74D;
        border-color: #f57c00;
    }

    .cuadro-leyenda.vacio {
        background: #e0e0e0;
        border-color: #999;
    }

    .contenedor-esquema {
        background: white;
        padding: 15px;
        border-radius: 4px;
    }

    .esquema-estante {
        font-size: 12px;
    }

    .fila-numeracion {
        display: flex;
        gap: 5px;
        margin-bottom: 10px;
        align-items: center;
    }

    .label-fila {
        width: 70px;
        text-align: right;
        font-weight: bold;
        font-size: 11px;
        color: #666;
    }

    .numero-columna {
        flex: 1;
        min-width: 50px;
        text-align: center;
        font-weight: bold;
        color: #666;
        font-size: 11px;
        padding: 5px 0;
        border-bottom: 2px solid #ddd;
    }

    .fila-estante {
        display: flex;
        gap: 5px;
        margin-bottom: 8px;
        align-items: center;
    }

    .celda-estante {
        flex: 1;
        min-width: 50px;
        height: 50px;
        border: 2px solid #999;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        position: relative;
        font-size: 11px;
        overflow: hidden;
        text-align: center;
        padding: 4px;
    }

    .celda-estante.ubicacion-actual {
        background: #4CAF50;
        color: white;
        border-color: #2e7d32;
        box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
        font-size: 13px;
        font-weight: bold;
    }

    .celda-estante.otros-productos {
        background: #FFB74D;
        color: #333;
        border-color: #f57c00;
    }

    .celda-estante.vacio {
        background: #f5f5f5;
        border-color: #ccc;
    }

    .codigo-celda {
        display: block;
        word-break: break-word;
        line-height: 1.2;
    }

    .productos-fila {
        margin-top: 20px;
        padding: 15px;
        background: white;
        border-radius: 4px;
        border-left: 4px solid #FF9800;
    }

    .productos-fila h4 {
        margin: 0 0 10px 0;
        color: #333;
    }

    .lista-productos-fila {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .item-producto-fila {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px;
        background: #f9f9f9;
        border-radius: 4px;
        font-size: 13px;
    }

    .codigo-fila {
        font-weight: bold;
        color: #2196F3;
        min-width: 80px;
    }

    .descripcion-fila {
        flex: 1;
        color: #666;
    }

    .cantidad-fila {
        color: #999;
        font-size: 12px;
    }

    /* Responsive */
    @media print {
        .botones-encabezado, button {
            display: none;
        }
        
        .seccion-detalles {
            box-shadow: none;
            border: 1px solid #ccc;
        }
    }
</style>