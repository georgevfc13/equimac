<!-- Vista: Formulario Crear/Editar Producto -->

<div class="seccion-formulario">
    <!-- Encabezado -->
    <div class="encabezado">
        <h2><?php echo $es_edicion ? 'Editar Producto' : 'Nuevo Producto'; ?></h2>
    </div>

    <!-- Formulario -->
    <form action="index.php?accion=guardar" method="POST" class="formulario-producto" id="formularioProducto">
        
        <?php if ($es_edicion): ?>
            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
        <?php endif; ?>

        <div class="seccion-formulario-grupo">
            <h3>📋 Información Básica</h3>

            <!-- Código -->
            <div class="grupo-form">
                <label for="codigo">Código de Producto <span class="requerido">*</span></label>
                <input type="text" id="codigo" name="codigo" class="input-form" 
                       value="<?php echo htmlspecialchars($producto['codigo'] ?? ''); ?>"
                       <?php echo $es_edicion ? 'readonly' : ''; ?>
                       placeholder="EQ-001" required>
                <small class="ayuda">Código único del producto (ej: EQ-001)</small>
            </div>

            <!-- Descripción -->
            <div class="grupo-form">
                <label for="descripcion">Descripción <span class="requerido">*</span></label>
                <input type="text" id="descripcion" name="descripcion" class="input-form" 
                       value="<?php echo htmlspecialchars($producto['descripcion'] ?? ''); ?>"
                       placeholder="Motor Eléctrico 1HP" required>
                <small class="ayuda">Descripción clara del producto</small>
            </div>

            <!-- Unidad -->
            <div class="grupo-form">
                <label for="unidad">Unidad de Medida <span class="requerido">*</span></label>
                <select id="unidad" name="unidad" class="input-form" required>
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($unidades as $unit): ?>
                        <option value="<?php echo htmlspecialchars($unit); ?>" 
                                <?php echo (isset($producto['unidad']) && $producto['unidad'] === $unit) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($unit); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Cantidad -->
            <div class="grupo-form">
                <label for="cantidad">Cantidad <span class="requerido">*</span></label>
                <input type="number" id="cantidad" name="cantidad" class="input-form" 
                       value="<?php echo intval($producto['cantidad'] ?? 0); ?>"
                       min="0" placeholder="0" required>
            </div>
        </div>

        <div class="seccion-formulario-grupo">
            <h3>🏷️ Información Adicional</h3>

            <!-- Marca -->
            <div class="grupo-form">
                <label for="marca">Marca</label>
                <input type="text" id="marca" name="marca" class="input-form" 
                       value="<?php echo htmlspecialchars($producto['marca'] ?? ''); ?>"
                       list="lista_marcas"
                       placeholder="Ej: Siemens, ABB, WEG...">
                <datalist id="lista_marcas">
                    <?php foreach ($marcas as $marca): ?>
                        <option value="<?php echo htmlspecialchars($marca); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>

            <!-- Equipo -->
            <div class="grupo-form">
                <label for="equipo">Equipo</label>
                <input type="text" id="equipo" name="equipo" class="input-form" 
                       value="<?php echo htmlspecialchars($producto['equipo'] ?? ''); ?>"
                       placeholder="Ej: Compresor, Bomba...">
            </div>

            <!-- Aplicación -->
            <div class="grupo-form">
                <label for="aplicacion">Aplicación</label>
                <input type="text" id="aplicacion" name="aplicacion" class="input-form" 
                       value="<?php echo htmlspecialchars($producto['aplicacion'] ?? ''); ?>"
                       placeholder="Ej: Aire acondicionado, Refrigeración...">
            </div>

            <!-- Tipo de Maquinaria -->
            <div class="grupo-form">
                <label for="tipo_maquinaria">Tipo de Maquinaria</label>
                <input type="text" id="tipo_maquinaria" name="tipo_maquinaria" class="input-form" 
                       value="<?php echo htmlspecialchars($producto['tipo_maquinaria'] ?? ''); ?>"
                       list="lista_tipos"
                       placeholder="Ej: Hidráulica, Neumática...">
                <datalist id="lista_tipos">
                    <?php foreach ($tipos_maquinaria as $tipo): ?>
                        <option value="<?php echo htmlspecialchars($tipo); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="seccion-formulario-grupo">
            <h3>📍 Ubicación en Estante <span class="requerido">*</span></h3>

            <!-- Estante -->
            <div class="grupo-form">
                <label for="estante">Estante <span class="requerido">*</span></label>
                <select id="estante" name="estante" class="input-form" required onchange="actualizarVisualizacionEstante()">
                    <option value="">-- Seleccionar Estante --</option>
                    <?php foreach ($estantes as $est): ?>
                        <option value="<?php echo $est['numero']; ?>" 
                                data-filas="<?php echo $est['filas']; ?>"
                                data-columnas="<?php echo $est['columnas']; ?>"
                                data-descripcion="<?php echo htmlspecialchars($est['descripcion']); ?>"
                                data-ubicacion="<?php echo htmlspecialchars($est['ubicacion']); ?>"
                                <?php echo (isset($producto['estante']) && $producto['estante'] == $est['numero']) ? 'selected' : ''; ?>>
                            Estante <?php echo $est['numero']; ?> - <?php echo htmlspecialchars($est['descripcion']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="ayuda">Selecciona el estante donde se guardará el producto</small>
            </div>

            <!-- Entrepaño (Fila) -->
            <div class="grupo-form">
                <label for="entrepaño">Fila <span class="requerido">*</span></label>
                <select id="entrepaño" name="entrepaño" class="input-form" required onchange="actualizarVisualizacionEstante()">
                    <option value="">-- Selecciona un estante primero --</option>
                </select>
                <small class="ayuda">Selecciona la fila del estante</small>
            </div>

            <!-- Vista previa del estante mejorada -->
            <div id="preview-estante" class="preview-estante-container" style="display: none; margin-top: 25px;">
                <div class="preview-info">
                    <h4 style="margin: 0 0 15px 0; color: #333; font-size: 16px;">
                        <span style="color: #FF9800;">🗂️</span> Haz clic en una posición para seleccionarla
                    </h4>
                </div>

                <div class="estante-3d" id="estante-visual">
                    <!-- Se rellena con JavaScript -->
                </div>

                <!-- Leyenda de colores -->
                <div class="leyenda-formulario" style="margin-top: 20px; padding: 15px; background: #f5f5f5; border-radius: 6px; border: 1px solid #ddd;">
                    <strong style="display: block; margin-bottom: 10px; color: #333;">📋 Leyenda:</strong>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 30px; height: 30px; background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%); border: 2px solid #ccc; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #666;">📦</div>
                            <span style="font-size: 12px;">Posición Libre</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 30px; height: 30px; background: linear-gradient(135deg, #ef5350 0%, #e53935 100%); border: 2px solid #c62828; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold;">❌</div>
                            <span style="font-size: 12px;">Posición Ocupada</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 30px; height: 30px; background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); border: 2px solid #2e7d32; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold;">✓</div>
                            <span style="font-size: 12px;">Seleccionada</span>
                        </div>
                    </div>
                </div>

                <!-- Información de selección -->
                <div id="info-posicion-seleccionada" class="info-posicion-form" style="margin-top: 20px; padding: 15px; background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); border-left: 4px solid #2196F3; border-radius: 6px; display: none;">
                    <strong style="color: #1565c0;">✓ Ubicación seleccionada:</strong><br>
                    <span id="texto-posicion" style="font-size: 15px; color: #6a1b9a; font-weight: bold; margin-top: 5px; display: block;"></span>
                </div>
            </div>

            <!-- Posición (oculto, se maneja con JavaScript) -->
            <input type="hidden" id="posicion" name="posicion" value="1">
        </div>

        <div class="seccion-formulario-grupo">
            <h3>📦 Información de Recepción</h3>

            <!-- De quién llegó -->
            <div class="grupo-form">
                <label for="de_quien_llego">De parte de quién llegó</label>
                <input type="text" id="de_quien_llego" name="de_quien_llego" class="input-form" 
                       value="<?php echo htmlspecialchars($producto['de_quien_llego'] ?? ''); ?>"
                       placeholder="Ej: Juan García, Proveedor ABC...">
                <small class="ayuda">Nombre del remitente o proveedor</small>
            </div>

            <!-- Precio pagado -->
            <div class="grupo-form">
                <label for="precio_pagado">Precio Pagado</label>
                <input type="number" id="precio_pagado" name="precio_pagado" class="input-form" 
                       value="<?php echo isset($producto['precio_pagado']) && $producto['precio_pagado'] ? number_format($producto['precio_pagado'], 2, '.', '') : ''; ?>"
                       min="0" step="0.01" placeholder="0.00"
                       onchange="formatearMoneda(this)">
                <small class="ayuda">Costo unitario del producto</small>
            </div>

            <!-- Quién lo recibió -->
            <div class="grupo-form">
                <label for="quien_recibio">Quién lo recibió</label>
                <input type="text" id="quien_recibio" name="quien_recibio" class="input-form" 
                       value="<?php echo htmlspecialchars($producto['quien_recibio'] ?? ''); ?>"
                       placeholder="Ej: Carlos Rodríguez, Almacenero...">
                <small class="ayuda">Nombre de la persona que recibió el producto</small>
            </div>
        </div>

        <div class="seccion-formulario-grupo">
            <h3>⚙️ Estado</h3>

            <!-- Estado -->
            <div class="grupo-form">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" class="input-form">
                    <option value="">-- Sin estado --</option>
                    <option value="Activo" <?php echo (isset($producto['estado']) && $producto['estado'] === 'Activo') ? 'selected' : ''; ?>>Activo</option>
                    <option value="Inactivo" <?php echo (isset($producto['estado']) && $producto['estado'] === 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                    <option value="Dañado" <?php echo (isset($producto['estado']) && $producto['estado'] === 'Dañado') ? 'selected' : ''; ?>>Dañado</option>
                    <option value="Mantenimiento" <?php echo (isset($producto['estado']) && $producto['estado'] === 'Mantenimiento') ? 'selected' : ''; ?>>Mantenimiento</option>
                </select>
            </div>
        </div>

        <!-- Acciones -->
        <div class="grupo-acciones">
            <button type="submit" class="btn btn-primary btn-grande">
                <?php echo $es_edicion ? '✓ Guardar Cambios' : '✓ Crear Producto'; ?>
            </button>
            <a href="index.php?accion=listar" class="btn btn-secundario btn-grande">
                ← Volver al Inventario
            </a>
        </div>
    </form>

    <!-- Información Adicional -->
    <div class="info-formulario">
        <h4>💡 Recomendaciones</h4>
        <ul>
            <li>El código debe ser único para cada producto</li>
            <li>La descripción debe ser clara y concisa</li>
            <li>Los campos marcados con * son obligatorios</li>
            <li>Selecciona una ubicación específica en el estante haciendo clic en una posición</li>
            <li>Completa los campos adicionales para mejor organización y búsqueda</li>
        </ul>
    </div>
</div>

<script>
// Obtener estantes dinámicamente
const estantes = <?php echo json_encode($estantes); ?>;
let posicionSeleccionada = null;

function actualizarVisualizacionEstante() {
    const estanteSelect = document.getElementById('estante');
    const entrepaño_select = document.getElementById('entrepaño');
    const previestante = document.getElementById('preview-estante');
    
    const estante_id = estanteSelect.value;
    
    if (!estante_id) {
        entrepaño_select.innerHTML = '<option value="">-- Selecciona un estante primero --</option>';
        previestante.style.display = 'none';
        posicionSeleccionada = null;
        return;
    }
    
    const estante = estantes.find(e => parseInt(e.numero) === parseInt(estante_id));
    if (!estante) return;
    
    // Actualizar opciones de entrepaño
    let html = '<option value="">-- Seleccionar Fila --</option>';
    for (let i = 1; i <= parseInt(estante.filas); i++) {
        html += `<option value="${i}">Fila ${i}</option>`;
    }
    entrepaño_select.innerHTML = html;
    
    // Restaurar valor seleccionado si existe
    const productoEntrepaño = <?php echo isset($producto['entrepaño']) ? (int)$producto['entrepaño'] : 'null'; ?>;
    const productoEstante = <?php echo isset($producto['estante']) ? (int)$producto['estante'] : 'null'; ?>;
    
    if (productoEstante === parseInt(estante_id) && productoEntrepaño) {
        entrepaño_select.value = productoEntrepaño;
    }
    
    // Si hay fila seleccionada, mostrar previsualización
    if (entrepaño_select.value) {
        previestante.style.display = 'block';
        posicionSeleccionada = null;
        const estante = estantes.find(e => parseInt(e.numero) === parseInt(estante_id));
        if (estante) {
            dibujarEstanteRealista(estante, parseInt(entrepaño_select.value));
        }
    }
}

function dibujarEstanteRealista(estante, entrepaño_seleccionado) {
    const contenedor = document.getElementById('estante-visual');
    const numPosiciones = 5;
    
    if (!contenedor) return;
    
    const estanteNum = document.getElementById('estante').value;
    
    // Cargar posiciones ocupadas vía AJAX
    fetch(`api/obtener_posiciones.php?estante=${estanteNum}&entrepaño=${entrepaño_seleccionado}`)
        .then(response => response.json())
        .then(data => {
            const posicionesOcupadas = data.posiciones || [];
            
            // Información del estante
            let html = `<div class="estante-info-header">
                <span class="estante-num-grande">${estante.numero}</span>
                <div class="estante-datos">
                    <div><strong>${estante.descripcion || 'Estante'}</strong></div>
                    <div style="font-size: 12px; color: #666;">📍 ${estante.ubicacion || 'Zona'}</div>
                </div>
            </div>`;
            
            html += '<div class="estante-compartimientos">';
            
            const filaNum = parseInt(entrepaño_seleccionado);
            if (!filaNum || filaNum < 1) {
                contenedor.innerHTML = '<p>Selecciona una fila válida</p>';
                return;
            }
            
            html += `<div class="fila-estante-grande" data-fila="${filaNum}">
                <div class="etiqueta-fila-grande">Fila ${filaNum}</div>
                <div class="posiciones-grid">`;
            
            for (let pos = 1; pos <= numPosiciones; pos++) {
                const posicionOcupada = posicionesOcupadas.find(p => parseInt(p.posicion) === pos);
                const esSeleccionado = posicionSeleccionada === pos;
                const claseSeleccion = esSeleccionado ? 'posicion-seleccionada' : '';
                const claseOcupada = posicionOcupada ? 'posicion-ocupada' : '';
                
                html += `<div class="compartimiento-estante ${claseSeleccion} ${claseOcupada}" 
                              data-posicion="${pos}" 
                              onclick="seleccionarPosicion(${filaNum}, ${pos})"
                              title="${posicionOcupada ? 'Ocupada: ' + posicionOcupada.codigo : 'Libre'}">
                    <div class="numero-posicion">${pos}</div>
                    <div class="icono-posicion">${posicionOcupada ? '❌' : '📦'}</div>
                    ${posicionOcupada ? `<small style="font-size: 10px; color: #fff; opacity: 0.8;">${posicionOcupada.codigo}</small>` : ''}
                </div>`;
            }
            
            html += '</div></div></div>';
            
            contenedor.innerHTML = html;
        })
        .catch(error => {
            console.error('Error al cargar posiciones:', error);
            contenedor.innerHTML = '<p style="color: red;">Error al cargar posiciones ocupadas</p>';
        });
}


function seleccionarPosicion(fila, posicion) {
    // Verificar si la posición está ocupada
    const compartimientosEnFila = document.querySelectorAll(`.compartimiento-estante[data-posicion="${posicion}"]`);
    if (compartimientosEnFila.length > 0) {
        if (compartimientosEnFila[0].classList.contains('posicion-ocupada')) {
            alert('❌ Esta posición ya está ocupada.\n\nSelecciona otra posición libre.');
            return;
        }
    }
    
    // Actualizar posición seleccionada
    posicionSeleccionada = posicion;
    
    // Actualizar campo oculto
    const posicionInput = document.getElementById('posicion');
    if (posicionInput) {
        posicionInput.value = posicion;
    }
    
    // Actualizar visualización
    const compartimientos = document.querySelectorAll('.compartimiento-estante');
    compartimientos.forEach(comp => {
        comp.classList.remove('posicion-seleccionada');
    });
    
    // Marcar el compartimiento clickeado
    if (compartimientosEnFila.length > 0) {
        compartimientosEnFila[0].classList.add('posicion-seleccionada');
    }
    
    // Mostrar información de selección
    const infoDiv = document.getElementById('info-posicion-seleccionada');
    const textoPos = document.getElementById('texto-posicion');
    const estanteNum = document.getElementById('estante').value;
    
    if (infoDiv && textoPos && estanteNum) {
        textoPos.textContent = `Estante ${estanteNum} • Fila ${fila} • Posición ${posicion}`;
        infoDiv.style.display = 'block';
        
        // Scroll para mostrar la información
        setTimeout(() => {
            infoDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
    }
}

// Event listeners
document.getElementById('estante').addEventListener('change', function() {
    posicionSeleccionada = null;
    actualizarVisualizacionEstante();
});

document.getElementById('entrepaño').addEventListener('change', function() {
    posicionSeleccionada = null;
    const estanteSelect = document.getElementById('estante');
    const estante_id = estanteSelect.value;
    if (estante_id && this.value) {
        const estante = estantes.find(e => e.numero == estante_id);
        if (estante) {
            document.getElementById('preview-estante').style.display = 'block';
            dibujarEstanteRealista(estante, this.value);
        }
    }
});

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', function() {
    const estanteSelect = document.getElementById('estante');
    const entrepaño_select = document.getElementById('entrepaño');
    
    if (!estanteSelect || !entrepaño_select) return;
    
    const estante_id = estanteSelect.value;
    const entrepaño_val = entrepaño_select.value;
    
    if (estante_id && entrepaño_val) {
        const estante = estantes.find(e => parseInt(e.numero) === parseInt(estante_id));
        if (estante) {
            document.getElementById('preview-estante').style.display = 'block';
            dibujarEstanteRealista(estante, parseInt(entrepaño_val));
            
            // Si hay posición guardada, mostrarla
            const posicionInput = document.getElementById('posicion');
            if (posicionInput && posicionInput.value) {
                posicionSeleccionada = parseInt(posicionInput.value);
            }
        }
    }
});

// Validación antes de enviar
document.getElementById('formularioProducto').addEventListener('submit', function(e) {
    const codigo = document.getElementById('codigo').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();
    const unidad = document.getElementById('unidad').value;
    const cantidad = parseInt(document.getElementById('cantidad').value);
    const estante = document.getElementById('estante').value;
    const entrepaño = document.getElementById('entrepaño').value;
    const posicion = document.getElementById('posicion').value;

    if (!codigo || !descripcion || !unidad || !estante || !entrepaño) {
        e.preventDefault();
        alert('Por favor completa todos los campos requeridos');
        return false;
    }

    if (!posicion) {
        e.preventDefault();
        alert('Por favor selecciona una posición en el estante');
        return false;
    }

    if (cantidad < 0) {
        e.preventDefault();
        alert('La cantidad no puede ser negativa');
        return false;
    }
});
</script>