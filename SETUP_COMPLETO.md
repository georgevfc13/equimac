# 🚀 GUÍA COMPLETA DE INSTALACIÓN Y CONFIGURACIÓN - EQUIMAC

## ✅ Estado Actual del Proyecto

El proyecto EQUIMAC está **100% funcional y listo para usar**. Todos los archivos han sido validados y corregidos.

### ✓ Lo que está listo:
- ✅ Arquitectura MVC completa implementada
- ✅ Base de datos MySQL configurada
- ✅ Todos los archivos PHP sin errores de sintaxis
- ✅ Sistema de validaciones (cliente + servidor)
- ✅ Interfaz responsiva (HTML5 + CSS3)
- ✅ Funciones JavaScript completas
- ✅ Seguridad con Prepared Statements
- ✅ Datos iniciales cargados
- ✅ 5 estantes de ejemplo preconfigurados
- ✅ 10 productos de ejemplo cargados

---

## 📋 REQUISITOS PREVIOS

Antes de comenzar, asegúrate de tener:

1. **XAMPP Instalado** (versión 7.4 o superior)
   - Incluye Apache, PHP 7.4+, MySQL 5.7+
   - Descargar: https://www.apachefriends.org/

2. **Archivo del Proyecto**
   - Ubicación: `C:\xampp\htdocs\equimac\`

3. **Navegador Moderno**
   - Chrome, Firefox, Edge o Safari

---

## 🔧 INSTALACIÓN PASO A PASO

### PASO 1: Verificar la Estructura del Proyecto

El proyecto debe estar en:
```
C:\xampp\htdocs\equimac\
├── config/
│   └── database.php
├── controllers/
│   └── InventarioController.php
├── models/
│   └── Inventario.php
├── views/
│   ├── layout.php
│   ├── lista.php
│   ├── formulario.php
│   ├── estantes.php
│   └── detalles.php
├── asset/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── index.php
├── database.sql
└── diagnostico.php
```

✅ **Verificación**: Abre `C:\xampp\htdocs\equimac\` en el explorador y confirma que ves estas carpetas.

---

### PASO 2: Iniciar XAMPP

1. **Abre XAMPP Control Panel** (por defecto en `C:\xampp\xampp-control.exe`)

2. **Inicia Apache y MySQL**:
   ```
   Apache: Click "Start" (debe decir "Running" en verde)
   MySQL:  Click "Start" (debe decir "Running" en verde)
   ```

3. **Verifica que inició correctamente**:
   - Abre navegador
   - Ve a: `http://localhost`
   - Deberías ver la página de inicio de XAMPP

✅ **Listo**: XAMPP iniciado correctamente

---

### PASO 3: Crear la Base de Datos

#### Opción A: Usando phpMyAdmin (RECOMENDADO)

1. **Abre phpMyAdmin**:
   - Navegador: `http://localhost/phpmyadmin`
   - Deberías ver una página azul/gris

2. **Crear nueva base de datos**:
   - Click izquierdo en "Nuevo" (arriba a la izquierda)
   - Nombre: `equimac`
   - Cotejamiento: `utf8mb4_unicode_ci`
   - Click en "Crear"

3. **Ejecutar el script SQL**:
   - Click en la pestaña "SQL" (en la BD equimac)
   - Abre el archivo `C:\xampp\htdocs\equimac\database.sql`
   - Copia TODO el contenido
   - Pégalo en phpMyAdmin
   - Click en "Ejecutar" (botón azul abajo a la derecha)

4. **Verificar**:
   - Click en "Estructuras" (arriba a la izquierda)
   - Deberías ver 2 tablas:
     - `inventario` (con 10 productos de ejemplo)
     - `estantes` (con 5 estantes preconfigurados)

✅ **Listo**: Base de datos creada con datos iniciales

#### Opción B: Usando MySQL Command Line (Alternativa)

```bash
# Abre cmd y ejecuta:
cd C:\xampp\mysql\bin

# Conectar a MySQL
mysql -u root

# Copiar y pegar el contenido de database.sql
USE equimac;
... (todo el contenido de database.sql)
```

---

### PASO 4: Verificar la Instalación

1. **Accede a la página de diagnóstico**:
   - Navegador: `http://localhost/equimac/diagnostico.php`

2. **Verifica que TODO muestre ✓ (checks verdes)**:
   ```
   ✓ PHP Version
   ✓ Extensión mysqli
   ✓ MySQL Conexión
   ✓ BD equimac
   ✓ Tablas en BD
   ✓ Archivos necesarios
   ```

3. **Si algo muestra ✗ (rojo)**:
   - Revisa la sección "Recomendaciones"
   - Sigue los pasos sugeridos

✅ **Listo**: Sistema verificado

---

### PASO 5: Acceder al Sistema

1. **Abre el navegador**:
   - Ve a: `http://localhost/equimac`

2. **Deberías ver**:
   - Barra de navegación azul con "EQUIMAC"
   - Título "📦 Inventario de Productos"
   - Estadísticas (10 productos, 5 estantes)
   - Tabla con los 10 productos de ejemplo

3. **Prueba las funciones**:
   - ✅ Buscar un producto (busca "Motor" en la tabla)
   - ✅ Agregar nuevo producto (botón "Nuevo Producto")
   - ✅ Editar un producto (click en "✎ Editar")
   - ✅ Ver detalles (click en "👁 Detalles")
   - ✅ Ver estantes (menú "🗂️ Estantes")

✅ **¡LISTO! 🎉 SISTEMA FUNCIONANDO**

---

## 🔍 SOLUCIÓN DE PROBLEMAS

### Problema: "Error en conexión a base de datos"

**Causa**: MySQL no está iniciado o credenciales incorrectas

**Solución**:
1. Abre XAMPP Control Panel
2. Verifica que MySQL esté en "Running" (verde)
3. Si no está, click en "Start"
4. Espera 5 segundos y recarga la página

---

### Problema: "No se encuentran los datos iniciales"

**Causa**: El script SQL no se ejecutó completamente

**Solución**:
1. Ve a `http://localhost/phpmyadmin`
2. Click en "equimac" (base de datos)
3. Click en "SQL"
4. Borra lo anterior
5. Abre `C:\xampp\htdocs\equimac\database.sql`
6. Copia TODO y pégalo
7. Click en "Ejecutar"

---

### Problema: "Página en blanco o Error 404"

**Causa**: XAMPP no está iniciado o ruta incorrecta

**Solución**:
1. Verifica que Apache esté iniciado (Running en verde)
2. Verifica que el proyecto esté en `C:\xampp\htdocs\equimac\`
3. Intenta: `http://localhost/phpmyadmin` (para verificar que XAMPP funciona)

---

### Problema: "No me permite crear/editar productos"

**Causa**: Validaciones del servidor no pasadas

**Solución**:
1. Revisa los mensajes de error (aparecen en rojo)
2. Asegúrate que todos los campos requeridos estén completos
3. Verifica que el código del producto sea ÚNICO

---

## 📊 ESTRUCTURA DE LA BASE DE DATOS

### Tabla: `inventario`

```sql
- id (INT, clave primaria, auto-incremento)
- codigo (VARCHAR 50, ÚNICO) - Código del producto
- descripcion (VARCHAR 255) - Nombre/descripción
- unidad (VARCHAR 50) - Tipo de unidad (Pieza, Metro, etc.)
- cantidad (INT) - Cantidad disponible
- marca (VARCHAR 100) - Marca del producto
- equipo (VARCHAR 100) - Equipo asociado
- aplicacion (VARCHAR 100) - Aplicación/uso
- estante (INT) - Número de estante
- entrepaño (INT) - Fila dentro del estante
- estado (VARCHAR 50) - Estado (Activo, Inactivo, Dañado, Mantenimiento)
- tipo_maquinaria (VARCHAR 100) - Tipo de máquina
- fecha_creacion (TIMESTAMP) - Fecha de creación
- fecha_actualizacion (TIMESTAMP) - Última actualización
```

### Tabla: `estantes`

```sql
- id (INT, clave primaria, auto-incremento)
- numero (INT, ÚNICO) - Número de estante (1, 2, 3...)
- filas (INT) - Cantidad de filas (entrepaños)
- columnas (INT) - Cantidad de columnas (posiciones)
- descripcion (VARCHAR 255) - Descripción del estante
- ubicacion (VARCHAR 100) - Ubicación física
- fecha_creacion (TIMESTAMP) - Fecha de creación
```

---

## 🔐 SEGURIDAD

El sistema incluye:

✅ **Prepared Statements** - Previene SQL Injection
✅ **Escapado de HTML** - Previene XSS
✅ **Validaciones** - Cliente (JavaScript) + Servidor (PHP)
✅ **Gestión de Errores** - Errores privados, no públicos
✅ **Singleton Pattern** - Una única conexión a BD

---

## 📱 USO BÁSICO

### Crear un nuevo producto:
1. Click en "Nuevo Producto"
2. Completa los campos (los marcados con * son obligatorios)
3. Selecciona un estante y fila
4. Haz click en "Crear Producto"

### Editar un producto:
1. Busca el producto en la tabla
2. Click en "✎ Editar"
3. Modifica los datos que desees
4. Haz click en "Guardar Cambios"

### Eliminar un producto:
1. Busca el producto en la tabla
2. Click en "🗑 Eliminar"
3. Confirma la acción

### Ver estantes:
1. Click en "🗂️ Estantes" (menú superior)
2. Puedes filtrar por estante
3. Ves la distribución visual de los productos

### Buscar productos:
1. Usa el buscador "🔍 Buscar"
2. Busca por: código, descripción, marca, equipo

---

## 📞 CONTACTO Y SOPORTE

Si tienes problemas:

1. **Revisa el archivo diagnostico.php**: `http://localhost/equimac/diagnostico.php`
2. **Lee la documentación**: `C:\xampp\htdocs\equimac\README.md`
3. **Verifica la arquitectura**: `C:\xampp\htdocs\equimac\ARQUITECTURA.md`

---

## 🎉 ¡FELICIDADES!

Tu sistema **EQUIMAC** está 100% funcional.

Ahora puedes:
- ✅ Gestionar tu inventario completo
- ✅ Organizar productos en estantes
- ✅ Buscar rápidamente
- ✅ Generar reportes

**¡A disfrutar de la gestión de inventario profesional!**

---

*Última actualización: 21 de Abril de 2026*
*EQUIMAC v1.0 - Sistema de Inventario basado en MVC*
