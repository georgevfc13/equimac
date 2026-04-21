/**
 * Script: Inventario - Funciones JavaScript
 * Archivo: public/js/script.js
 */

/**
 * Confirmación antes de eliminar
 */
function confirmarEliminar(nombreProducto) {
    return confirm(
        `¿Estás seguro de que deseas eliminar el producto:\n\n"${nombreProducto}"?\n\nEsta acción no se puede deshacer.`
    );
}

/**
 * Validación del formulario en el lado del cliente
 */
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar alertas automáticamente después de 5 segundos
    const alertas = document.querySelectorAll('.alerta');
    alertas.forEach(alerta => {
        if (alerta.classList.contains('alerta-exito')) {
            setTimeout(() => {
                alerta.style.display = 'none';
            }, 5000);
        }
    });

    // Buscar en la tabla en tiempo real
    const inputBusqueda = document.querySelector('.input-busqueda');
    if (inputBusqueda) {
        inputBusqueda.addEventListener('keyup', function() {
            buscarEnTabla(this.value);
        });
    }
});

/**
 * Función para buscar en la tabla de inventario
 */
function buscarEnTabla(termino) {
    const filas = document.querySelectorAll('.fila-producto');
    termino = termino.toLowerCase();

    filas.forEach(fila => {
        const texto = fila.textContent.toLowerCase();
        if (texto.includes(termino)) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
}

/**
 * Formatea moneda en tiempo real
 */
function formatearMoneda(input) {
    let valor = parseFloat(input.value);
    if (!isNaN(valor)) {
        input.value = valor.toFixed(2);
    }
}

/**
 * Incrementar/Decrementar cantidad con botones
 */
function incrementarCantidad(inputId, incremento) {
    const input = document.getElementById(inputId);
    let valor = parseInt(input.value) || 0;
    valor += incremento;
    if (valor < 0) valor = 0;
    input.value = valor;
}

/**
 * Copiar al portapapeles
 */
function copiarAlPortapapeles(texto) {
    navigator.clipboard.writeText(texto).then(() => {
        mostrarNotificacion('Copiado al portapapeles', 'exito');
    }).catch(() => {
        mostrarNotificacion('Error al copiar', 'error');
    });
}

/**
 * Mostrar notificación temporal
 */
function mostrarNotificacion(mensaje, tipo = 'info') {
    const alerta = document.createElement('div');
    alerta.className = `alerta alerta-${tipo}`;
    alerta.innerHTML = `
        <span>${mensaje}</span>
        <span class="cerrar" onclick="this.parentElement.style.display='none';">&times;</span>
    `;
    
    const contenedor = document.querySelector('.contenido');
    if (contenedor) {
        contenedor.insertBefore(alerta, contenedor.firstChild);
        
        setTimeout(() => {
            alerta.style.display = 'none';
        }, 3000);
    }
}

/**
 * Exportar tabla a CSV
 */
function exportarTablaCSV() {
    const tabla = document.querySelector('.tabla-inventario');
    if (!tabla) return;

    let csv = [];
    const filas = tabla.querySelectorAll('tr');

    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td, th');
        const fila_csv = Array.from(celdas).map(celda => {
            return '"' + celda.textContent.trim().replace(/"/g, '""') + '"';
        }).join(',');
        csv.push(fila_csv);
    });

    const contenido = csv.join('\n');
    descargarArchivo(contenido, 'inventario.csv', 'text/csv;charset=utf-8;');
}

/**
 * Descargar archivo
 */
function descargarArchivo(contenido, nombre, tipo) {
    const link = document.createElement('a');
    const blob = new Blob([contenido], { type: tipo });
    link.setAttribute('href', URL.createObjectURL(blob));
    link.setAttribute('download', nombre);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Imprimir tabla
 */
function imprimirTabla() {
    window.print();
}

/**
 * Filtrar por categoría
 */
function filtrarPorCategoria(categoria) {
    if (!categoria) {
        location.href = 'index.php?accion=listar';
    } else {
        location.href = 'index.php?accion=listar&buscar=' + encodeURIComponent(categoria);
    }
}

/**
 * Validar cantidad (solo números positivos)
 */
function validarCantidad(input) {
    let valor = input.value;
    valor = valor.replace(/[^\d]/g, '');
    input.value = valor;
}

/**
 * Validar precio (números con decimales)
 */
function validarPrecio(input) {
    let valor = input.value;
    valor = valor.replace(/[^\d.]/g, '');
    
    // Evitar múltiples puntos
    const puntos = valor.split('.').length - 1;
    if (puntos > 1) {
        valor = valor.replace(/\.+/g, '.');
    }
    
    input.value = valor;
}

/**
 * Enfoque de formulario mejorado
 */
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.input-form, .textarea-form');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.borderColor = 'var(--color-primario)';
        });
        
        input.addEventListener('blur', function() {
            this.style.borderColor = '';
        });
    });
});

/**
 * Dark Mode Toggle (Opcional)
 */
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
}

// Cargar preferencia de dark mode al iniciar
if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark-mode');
}
