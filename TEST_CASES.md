# 🧪 TEST CASES - Validación de Funcionalidades

## Test Case 1: Crear Producto con Nueva Información

### Precondiciones:
- Sistema iniciado
- Base de datos actualizada con nuevas columnas

### Pasos:
1. Ir a "Nuevo Producto"
2. Llenar datos básicos:
   - Código: TEST-001
   - Descripción: Producto de Prueba
   - Cantidad: 5
   - Unidad: Unidades
3. Seleccionar Estante: 1
4. Seleccionar Fila: 2
5. Observar el formulario

### Resultados Esperados:
✅ Se cargan automáticamente posiciones ocupadas
✅ Posiciones ocupadas muestran EN ROJO (❌)
✅ Posiciones libres muestran EN BLANCO (📦)
✅ Leyenda visible al pie del estante
✅ Se puede hacer clic en posición libre

### Si hay posiciones ocupadas:
⚠️ Al intentar clic en roja, muestra alerta: "Esta posición ya está ocupada"

---

## Test Case 2: Llenar Información de Recepción

### Pasos:
1. En el mismo formulario, desplazar hacia abajo
2. Encontrar sección "📦 Información de Recepción"
3. Llenar campos:
   - De parte de quién llegó: "Proveedor XYZ"
   - Precio Pagado: "1500.50"
   - Quién lo recibió: "Juan Pérez"
4. Clic en "Guardar"

### Resultados Esperados:
✅ Se aceptan todos los campos
✅ Precio se formatea correctamente
✅ Producto se crea exitosamente
✅ Mensaje de confirmación

---

## Test Case 3: Ver Detalles en Nueva Pestaña

### Precondiciones:
- Debe haber al menos un producto guardado

### Pasos:
1. Ir a "Estantes"
2. Encontrar una posición ocupada (azul)
3. Hacer clic DIRECTAMENTE en la posición

### Resultados Esperados:
✅ Se abre NUEVA PESTAÑA (no la misma)
✅ Se carga página de detalles
✅ URL contiene parámetro: ?accion=detalles&id=X
✅ Se visualiza toda la información

### En la página de detalles:
✅ Sección "Información de Recepción" visible
✅ Se muestra "De quién llegó"
✅ Se muestra "Precio Pagado" en formato moneda
✅ Se muestra "Quién lo recibió"
✅ Esquema del estante visible

---

## Test Case 4: Validación de Posiciones Ocupadas

### Precondiciones:
- Dos productos creados en la misma fila

### Pasos:
1. Editar uno de los productos
2. Cambiar a una fila que tenga otro producto
3. Intentar seleccionar la posición ocupada

### Resultados Esperados:
⚠️ Alerta: "❌ Esta posición ya está ocupada"
❌ Selección NO se realiza
✅ Se puede seleccionar otra posición libre

---

## Test Case 5: Impresión de Detalles

### Pasos:
1. En página de detalles, desplazar al final
2. Buscar botón "🖨️ Imprimir Información"
3. Hacer clic

### Resultados Esperados:
✅ Se abre diálogo de impresión
✅ Se incluye toda la información del producto
✅ Se incluye el esquema del estante
✅ Se puede imprimir a PDF o papel

---

## Test Case 6: Actualizar Producto Existente

### Precondiciones:
- Producto ya existe en la BD

### Pasos:
1. Ir a Lista de Productos
2. Clic en "Editar"
3. Cambiar datos de recepción
4. Cambiar posición (si hay posiciones libres)
5. Guardar

### Resultados Esperados:
✅ Se cargan datos previos
✅ Posición anterior se resalta en VERDE
✅ Puede cambiar a nueva posición
✅ Cambios se guardan correctamente

---

## Test Case 7: Validar Campo de Precio

### Pasos:
1. En formulario, campo "Precio Pagado"
2. Escribir: "1500.5"
3. Presionar Tab o cambiar foco
4. Observar valor

### Resultados Esperados:
✅ Se formatea a "1500.50"
✅ En detalles aparece como "$1,500.50"
✅ Acepta decimales correctamente

---

## Test Case 8: Verificar AJAX de Posiciones

### Requisito técnico:
- Consola del navegador (F12)

### Pasos:
1. Abrir formulario de crear producto
2. Abrir consola (F12) → Network
3. Seleccionar Estante y Fila
4. Observar llamadas AJAX

### Resultados Esperados:
✅ Se realiza llamada a: api/obtener_posiciones.php
✅ Parámetros: estante=X&entrepaño=Y
✅ Respuesta JSON con posiciones ocupadas
✅ Se actualiza visualización sin recargar

---

## Checklist de Validación Final

- [ ] Base de datos tiene nuevas columnas
- [ ] API obtener_posiciones.php funciona
- [ ] Formulario muestra posiciones en ROJO
- [ ] No se pueden seleccionar posiciones ocupadas
- [ ] Nueva pestaña abre correctamente
- [ ] Detalles muestran información de recepción
- [ ] Precio formatea correctamente
- [ ] Se pueden crear productos nuevos
- [ ] Se pueden editar productos existentes
- [ ] Esquema visual funciona
- [ ] Leyenda es clara y visible

---

## 🐛 Reportar Problemas

Si algún test falla:
1. Verificar base de datos está actualizada
2. Verificar API obtener_posiciones.php existe
3. Revisar console del navegador (F12)
4. Verificar permisos de archivos

---

**Versión de Test**: 1.0
**Última Actualización**: 22 Abril 2026
**Estado**: Listo para Testing
