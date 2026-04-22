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
 
            <!-- Esquema Visual del Estante - MEJORADO -->
            <div class="tarjeta-seccion seccion-detalles-estante">
                <h3 class="titulo-seccion">🗂️ Esquema del Estante <?php echo $estante['numero']; ?></h3>
                
                <!-- Leyenda -->
                <div class="leyenda-estante-detallada">
                    <div class="leyenda-item-estante">
                        <div class="color-leyenda-estante seleccionado"></div>
                        <span>Ubicación actual</span>
                    </div>
                    <div class="leyenda-item-estante">
                        <div class="color-leyenda-estante ocupado"></div>
                        <span>Otros productos</span>
                    </div>
                    <div class="leyenda-item-estante">
                        <div class="color-leyenda-estante vacio"></div>
                        <span>Espacio vacío</span>
                    </div>
                </div>
 
                <!-- Contenedor de esquema -->
                <div class="estante-esquema-container">
                    <?php if ($estante): ?>
                        <div class="esquema-estante-detallado">
                            <!-- Mostrar todas las filas -->
                            <?php for ($fila = $estante['filas']; $fila >= 1; $fila--): ?>
                                <div class="fila-esquema">
                                    <div class="etiqueta-fila-esquema">Fila <?php echo $fila; ?></div>
                                    <div class="posiciones-esquema">
                                        <?php 
                                            // Obtener productos en esta fila
                                            $productosEnFila = [];
                                            foreach ($productosEnUbicacion as $prod) {
                                                if ($prod['entrepaño'] == $fila) {
                                                    $productosEnFila[] = $prod;
                                                }
                                            }
                                            
                                            // Crear 5 posiciones
                                            for ($pos = 1; $pos <= 5; $pos++) {
                                                $tieneProducto = false;
                                                $productoEnPosicion = null;
                                                
                                                // Verificar si hay un producto en esta posición
                                                foreach ($productosEnFila as $prod) {
                                                    if (intval($prod['id']) === intval($producto['id'])) {
                                                        $tieneProducto = true;
                                                        $productoEnPosicion = $prod;
                                                        break;
                                                    }
                                                }
                                                
                                                $claseEstado = '';
                                                if ($tieneProducto) {
                                                    $claseEstado = 'seleccionado';
                                                } elseif (count($productosEnFila) > 0) {
                                                    $claseEstado = 'ocupado';
                                                } else {
                                                    $claseEstado = 'vacio';
                                                }
                                        ?>
                                            <div class="posicion-esquema <?php echo $claseEstado; ?>">
                                                <div class="numero-posicion-esquema"><?php echo $pos; ?></div>
                                                <?php if ($tieneProducto): ?>
                                                    <div class="codigo-posicion">✓</div>
                                                <?php elseif (count($productosEnFila) > 0): ?>
                                                    <div style="font-size: 16px; opacity: 0.6;">•</div>
                                                <?php endif; ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
 
                        <!-- Información de productos en la misma fila -->
                        <?php if (count($productosEnUbicacion) > 1): ?>
                            <div class="productos-fila-detalles" style="margin-top: 25px; padding: 20px; background: #f9f9f9; border-radius: 8px; border-left: 4px solid #FF9800;">
                                <h4 style="margin: 0 0 15px 0; color: #FF9800; display: flex; align-items: center; gap: 8px;">
                                    <span>📦</span> Otros productos en esta fila (Fila <?php echo $producto['entrepaño']; ?>)
                                </h4>
                                <div class="lista-productos-fila-detalles">
                                    <?php foreach ($productosEnUbicacion as $prod): ?>
                                        <?php if ($prod['id'] != $producto['id']): ?>
                                            <div class="item-producto-fila-detalles">
                                                <span class="codigo-fila-detalles">
                                                    <strong><?php echo htmlspecialchars($prod['codigo']); ?></strong>
                                                </span>
                                                <span class="descripcion-fila-detalles">
                                                    <?php echo htmlspecialchars($prod['descripcion']); ?>
                                                </span>
                                                <span class="cantidad-fila-detalles">
                                                    (<?php echo $prod['cantidad']; ?> <?php echo htmlspecialchars($prod['unidad']); ?>)
                                                </span>
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
                <div style="margin-top: 20px; text-align: center; padding-top: 20px; border-top: 1px solid #ddd;">
                    <button type="button" class="btn btn-secondary" onclick="window.print();" style="display: inline-flex; align-items: center; gap: 8px;">
                        <span>🖨️</span> Imprimir Información
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
 
    .productos-fila-detalles h4 {
        margin: 0 0 15px 0;
    }
 
    .lista-productos-fila-detalles {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
 
    .item-producto-fila-detalles {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        background: white;
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        font-size: 13px;
    }
 
    .codigo-fila-detalles {
        color: #2196F3;
        font-weight: bold;
        min-width: 80px;
    }
 
    .descripcion-fila-detalles {
        flex: 1;
        color: #666;
    }
 
    .cantidad-fila-detalles {
        color: #999;
        font-size: 12px;
    }
 
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