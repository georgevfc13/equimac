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
                <select id="estante" name="estante" class="input-form" required onchange="actualizarEntrepaños()">
                    <option value="">-- Seleccionar Estante --</option>
                    <?php foreach ($estantes as $est): ?>
                        <option value="<?php echo $est['numero']; ?>" 
                                data-filas="<?php echo $est['filas']; ?>"
                                data-columnas="<?php echo $est['columnas']; ?>"
                                <?php echo (isset($producto['estante']) && $producto['estante'] == $est['numero']) ? 'selected' : ''; ?>>
                            Estante <?php echo $est['numero']; ?> (<?php echo $est['descripcion']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="ayuda">Selecciona el estante donde se guardará el producto</small>
            </div>

            <!-- Entrepaño -->
            <div class="grupo-form">
                <label for="entrepaño">Entrepaño (Fila) <span class="requerido">*</span></label>
                <select id="entrepaño" name="entrepaño" class="input-form" required>
                    <option value="">-- Selecciona un estante primero --</option>
                </select>
                <small class="ayuda">Selecciona la fila/entrepaño del estante</small>
            </div>

            <!-- Vista previa del estante -->
            <div id="preview-estante" style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 5px; display: none;">
                <p style="margin-top: 0; font-weight: bold; color: #333;">📋 Vista previa de ubicación:</p>
                <div id="estante-visual"></div>
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
            <li>La ubicación (Estante y Entrepaño) es necesaria para encontrar el producto</li>
            <li>Completa los campos adicionales para mejor organización y búsqueda</li>
        </ul>
    </div>
</div>

<script>
// Obtener estantes dinámicamente
const estantes = <?php echo json_encode($estantes); ?>;

function actualizarEntrepaños() {
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
    let html = '<option value="">-- Seleccionar Entrepaño --</option>';
    for (let i = 1; i <= estante.filas; i++) {
        html += `<option value="${i}">Fila ${i}</option>`;
    }
    entrepaño_select.innerHTML = html;
    
    // Restaurar valor seleccionado si existe
    const productoEntrepaño = <?php echo isset($producto['entrepaño']) ? $producto['entrepaño'] : 'null'; ?>;
    const productoEstante = <?php echo isset($producto['estante']) ? $producto['estante'] : 'null'; ?>;
    
    if (productoEstante == estante_id && productoEntrepaño) {
        entrepaño_select.value = productoEntrepaño;
    }
    
    // Mostrar previsualización
    previestante.style.display = 'block';
    dibujarEstante(estante, entrepaño_select.value);
}

function dibujarEstante(estante, entrepaño_seleccionado) {
    const contenedor = document.getElementById('estante-visual');
    let html = `<div style="font-size: 12px; margin: 10px 0;">Estante ${estante.numero}: ${estante.filas} filas × ${estante.columnas} columnas</div>`;
    html += '<div style="display: flex; flex-direction: column; gap: 8px;">';
    
    for (let fila = 1; fila <= estante.filas; fila++) {
        html += '<div style="display: flex; gap: 5px; align-items: center;">';
        html += `<div style="width: 60px; text-align: right; font-weight: bold; font-size: 13px;">Fila ${fila}</div>`;
        
        for (let col = 1; col <= estante.columnas; col++) {
            const esSeleccionado = fila == entrepaño_seleccionado;
            const bgcolor = esSeleccionado ? '#4CAF50' : '#e0e0e0';
            const color = esSeleccionado ? 'white' : '#666';
            
            html += `<div style="width: 40px; height: 40px; background: ${bgcolor}; border: 2px solid #999; border-radius: 3px; display: flex; align-items: center; justify-content: center; color: ${color}; font-weight: bold; font-size: 11px;">${col}</div>`;
        }
        html += '</div>';
    }
    
    html += '</div>';
    contenedor.innerHTML = html;
}

// Event listeners
document.getElementById('estante').addEventListener('change', actualizarEntrepaños);
document.getElementById('entrepaño').addEventListener('change', function() {
    const estanteSelect = document.getElementById('estante');
    const estante_id = estanteSelect.value;
    if (estante_id) {
        const estante = estantes.find(e => e.numero == estante_id);
        dibujarEstante(estante, this.value);
    }
});

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', function() {
    // Si estamos editando, cargar los entrepaños
    const estante_id = document.getElementById('estante').value;
    if (estante_id) {
        actualizarEntrepaños();
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

    if (!codigo || !descripcion || !unidad || !estante || !entrepaño) {
        e.preventDefault();
        alert('Por favor completa todos los campos requeridos');
        return false;
    }

    if (cantidad < 0) {
        e.preventDefault();
        alert('La cantidad no puede ser negativa');
        return false;
    }
});
</script>