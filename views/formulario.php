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
            <h3>Información Básica</h3>

            <!-- Código -->
            <div class="grupo-form">
                <label for="codigo">Código de Producto <span class="requerido">*</span></label>
                <input type="text" id="codigo" name="codigo" class="input-form" 
                       value="<?php echo htmlspecialchars($producto['codigo'] ?? ''); ?>"
                       <?php echo $es_edicion ? 'readonly' : ''; ?>
                       placeholder="EQ-001" required>
                <small class="ayuda">Código único del producto (ej: EQ-001)</small>
            </div>

            <!-- Nombre -->
            <div class="grupo-form">
                <label for="nombre">Nombre del Producto <span class="requerido">*</span></label>
                <input type="text" id="nombre" name="nombre" class="input-form" 
                       value="<?php echo htmlspecialchars($producto['nombre'] ?? ''); ?>"
                       placeholder="Motor Eléctrico 1HP" required>
            </div>

            <!-- Descripción -->
            <div class="grupo-form">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="textarea-form" 
                          placeholder="Detalles adicionales del producto..."
                          rows="3"><?php echo htmlspecialchars($producto['descripcion'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="seccion-formulario-grupo">
            <h3>Inventario y Precios</h3>

            <!-- Cantidad -->
            <div class="grupo-form">
                <label for="cantidad">Cantidad <span class="requerido">*</span></label>
                <input type="number" id="cantidad" name="cantidad" class="input-form" 
                       value="<?php echo intval($producto['cantidad'] ?? 0); ?>"
                       min="0" placeholder="0" required>
            </div>

            <!-- Precio Unitario -->
            <div class="grupo-form">
                <label for="precio_unitario">Precio Unitario ($) <span class="requerido">*</span></label>
                <input type="number" id="precio_unitario" name="precio_unitario" class="input-form" 
                       value="<?php echo floatval($producto['precio_unitario'] ?? 0); ?>"
                       min="0" step="0.01" placeholder="0.00" required>
            </div>

            <!-- Categoría -->
            <div class="grupo-form">
                <label for="categoria">Categoría</label>
                <div class="selector-categoria">
                    <select id="categoria" name="categoria" class="input-form">
                        <option value="">-- Seleccionar o crear nueva --</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" 
                                    <?php echo (isset($producto['categoria']) && $producto['categoria'] === $cat) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" id="categoria_nueva" name="categoria_nueva" class="input-form" 
                           placeholder="O escribe una nueva categoría..." style="display: none;">
                </div>
            </div>

            <!-- Estado -->
            <div class="grupo-form">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" class="input-form">
                    <option value="activo" <?php echo (isset($producto['estado']) && $producto['estado'] === 'activo') ? 'selected' : 'selected'; ?>>Activo</option>
                    <option value="inactivo" <?php echo (isset($producto['estado']) && $producto['estado'] === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                    <option value="descontinuado" <?php echo (isset($producto['estado']) && $producto['estado'] === 'descontinuado') ? 'selected' : ''; ?>>Descontinuado</option>
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
            <li>Utiliza códigos descriptivos (ej: EQ para equipos, MT para motores)</li>
            <li>Los campos marcados con * son obligatorios</li>
            <li>Puedes crear nuevas categorías escribiéndolas directamente</li>
        </ul>
    </div>
</div>

<script>
// Permitir crear nueva categoría
document.getElementById('categoria').addEventListener('change', function() {
    const nuevaInput = document.getElementById('categoria_nueva');
    if (this.value === '') {
        nuevaInput.style.display = 'block';
    } else {
        nuevaInput.style.display = 'none';
    }
});

// Actualizar campo de categoría si se escribe una nueva
document.getElementById('categoria_nueva').addEventListener('blur', function() {
    if (this.value.trim()) {
        document.getElementById('categoria').value = this.value.trim();
    }
});

// Validación antes de enviar
document.getElementById('formularioProducto').addEventListener('submit', function(e) {
    const codigo = document.getElementById('codigo').value.trim();
    const nombre = document.getElementById('nombre').value.trim();
    const cantidad = parseInt(document.getElementById('cantidad').value);
    const precio = parseFloat(document.getElementById('precio_unitario').value);

    if (!codigo || !nombre) {
        e.preventDefault();
        alert('Por favor completa los campos requeridos');
        return false;
    }

    if (cantidad < 0 || precio < 0) {
        e.preventDefault();
        alert('La cantidad y precio no pueden ser negativos');
        return false;
    }
});
</script>
