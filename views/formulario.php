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
    const estante_visual = document.getElementById('estante-visual');
    
    const estante_id = estanteSelect.value;
    
    if (!estante_id) {
        entrepaño_select.innerHTML = '<option value="">-- Selecciona un estante primero --</option>';
        previestante.style.display = 'none';
        return;
    }
    
    const estante = estantes.find(e => e.numero == estante_id);
    if (!estante) return;
    
    // Actualizar opciones de entrepaño
    let html = '<option value="">-- Seleccionar Fila --</option>';
    for (let i = 1; i <= estante.filas; i++) {
        html += `<option value="${i}">Fila ${i}</option>`;
    }
    entrepaño_select.innerHTML = html;
    
    // Restaurar valor seleccionado si existe
    const productoEntrepaño = <?php echo isset($producto['entrepaño']) ? $producto['entrepaño'] : 'null'; ?>;
    const productoEstante = <?php echo isset($producto['estante']) ? $producto['estante'] : 'null'; ?>;
    
    if (productoEstante == estante_id && productoEntrepaño) {
        entrepaño_select.value = productoEntrepaño;
        actualizarVisualizacionEstante();
    }
    
    // Si hay fila seleccionada, mostrar previsualización
    if (entrepaño_select.value) {
        previestante.style.display = 'block';
        dibujarEstanteRealista(estante, entrepaño_select.value);
    }
}
 
function dibujarEstanteRealista(estante, entrepaño_seleccionado) {
    const contenedor = document.getElementById('estante-visual');
    const numPosiciones = 5; // 5 posiciones por fila
    
    // Información del estante
    let html = `<div class="estante-info-header">
        <span class="estante-num-grande">${estante.numero}</span>
        <div class="estante-datos">
            <div><strong>${estante.descripcion}</strong></div>
            <div style="font-size: 12px; color: #666;">📍 ${estante.ubicacion}</div>
        </div>
    </div>`;
    
    html += '<div class="estante-compartimientos">';
    
    // Mostrar solo la fila seleccionada
    const filaNum = parseInt(entrepaño_seleccionado);
    
    html += `<div class="fila-estante-grande" data-fila="${filaNum}">
        <div class="etiqueta-fila-grande">Fila ${filaNum}</div>
        <div class="posiciones-grid">`;
    
    for (let pos = 1; pos <= numPosiciones; pos++) {
        const esSeleccionado = posicionSeleccionada === pos;
        const claseSeleccion = esSeleccionado ? 'posicion-seleccionada' : '';
        
        html += `<div class="compartimiento-estante ${claseSeleccion}" 
                      data-posicion="${pos}" 
                      onclick="seleccionarPosicion(${filaNum}, ${pos})">
            <div class="numero-posicion">${pos}</div>
            <div class="icono-posicion">📦</div>
        </div>`;
    }
    
    html += '</div></div></div>';
    
    contenedor.innerHTML = html;
}
 
function seleccionarPosicion(fila, posicion) {
    // Actualizar posición seleccionada
    posicionSeleccionada = posicion;
    
    // Actualizar campo oculto
    document.getElementById('posicion').value = posicion;
    
    // Actualizar visualización
    const compartimientos = document.querySelectorAll('.compartimiento-estante');
    compartimientos.forEach(comp => {
        comp.classList.remove('posicion-seleccionada');
    });
    event.target.closest('.compartimiento-estante').classList.add('posicion-seleccionada');
    
    // Mostrar información de selección
    const infoDiv = document.getElementById('info-posicion-seleccionada');
    const textoPos = document.getElementById('texto-posicion');
    const estanteNum = document.getElementById('estante').value;
    
    textoPos.textContent = `Estante ${estanteNum} • Fila ${fila} • Posición ${posicion}`;
    infoDiv.style.display = 'block';
    
    // Scroll para mostrar la información
    setTimeout(() => {
        infoDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }, 100);
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
    const estante_id = document.getElementById('estante').value;
    const entrepaño_val = document.getElementById('entrepaño').value;
    
    if (estante_id && entrepaño_val) {
        const estante = estantes.find(e => e.numero == estante_id);
        if (estante) {
            document.getElementById('preview-estante').style.display = 'block';
            dibujarEstanteRealista(estante, entrepaño_val);
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
