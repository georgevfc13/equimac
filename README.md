# 📦 SISTEMA DE INVENTARIO - EQUIMAC

Sistema de gestión de inventario completo basado en arquitectura **MVC** (Model-View-Controller), desarrollado con **PHP nativo** y **MySQL**.

---

## ✨ Características

- ✅ **Arquitectura MVC** estricta con separación clara de responsabilidades
- ✅ **CRUD Completo** - Crear, Leer, Actualizar y Eliminar productos
- ✅ **Base de Datos Local** - MySQL accesible vía localhost
- ✅ **Interfaz Responsiva** - Funciona en desktop, tablet y móvil
- ✅ **Validaciones** - En cliente (JavaScript) y servidor (PHP)
- ✅ **Búsqueda y Filtros** - Por código, nombre, categoría
- ✅ **Estadísticas** - Resumen del inventario en tiempo real
- ✅ **Seguridad** - Prepared statements contra SQL injection

---

## 🏗️ Estructura del Proyecto

```
equimac/
├── config/
│   └── database.php              # Configuración de conexión a BD
├── models/
│   └── Inventario.php            # Modelo - Acceso a datos
├── controllers/
│   └── InventarioController.php  # Controlador - Lógica de negocio
├── views/
│   ├── layout.php                # Layout principal
│   ├── lista.php                 # Vista listado de productos
│   └── formulario.php            # Vista formulario crear/editar
├── public/
│   ├── css/
│   │   └── style.css             # Estilos CSS
│   └── js/
│       └── script.js             # Scripts JavaScript
├── index.php                     # Punto de entrada (enrutador)
├── database.sql                  # Script SQL para crear BD
└── README.md                     # Este archivo
```

---

## 🔧 Stack Tecnológico

| Componente | Tecnología | Razón |
|-----------|-----------|--------|
| **Backend** | PHP 7.4+ | Lenguaje simple, poderoso, integrado con XAMPP |
| **Base de Datos** | MySQL 5.7+ | Confiable, escalable, acceso vía localhost |
| **Frontend** | HTML5 + CSS3 + JS Vanilla | Sin dependencias externas, control total |
| **Servidor Local** | XAMPP | Ambiente de desarrollo rápido y fácil |

---

## 📋 Requisitos Previos

1. **XAMPP** instalado (incluye Apache, PHP y MySQL)
   - Descargar desde: https://www.apachefriends.org/es/index.html

2. **Navegador web** moderno (Chrome, Firefox, Edge, Safari)

3. **Editor de código** (VS Code recomendado)

---

## 🚀 Instalación Paso a Paso

### 1️⃣ Ubicación del Proyecto

El proyecto debe estar en la carpeta de htdocs de XAMPP:
```
C:\xampp\htdocs\equimac\
```
*Este es el directorio donde se encuentra este README*

### 2️⃣ Iniciar XAMPP

1. Abre **XAMPP Control Panel**
2. Haz clic en **Start** junto a:
   - ✅ Apache
   - ✅ MySQL

```
[Apache]     Start ✓
[MySQL]      Start ✓
[FileZilla]  (opcional)
[Tomcat]     (opcional)
```

### 3️⃣ Crear la Base de Datos

#### Opción A: Usando phpMyAdmin (Recomendado)

1. Abre el navegador y ve a: **http://localhost/phpmyadmin**
2. Haz clic en **"Nuevo"** (arriba a la izquierda)
3. En la caja de texto de nombre, escribe: `equimac`
4. Haz clic en **"Crear"**
5. Se abrirá una pestaña **"equimac"**
6. Haz clic en la pestaña **"SQL"** (arriba)
7. Copia todo el contenido de `database.sql` de este proyecto
8. Pégalo en el editor SQL
9. Haz clic en **"Ejecutar"** (abajo)

#### Opción B: Línea de Comando

```bash
# Abre el terminal/CMD en la carpeta equimac
cd C:\xampp\htdocs\equimac

# Inicia MySQL
C:\xampp\mysql\bin\mysql -u root

# En MySQL, ejecuta:
mysql> SOURCE database.sql;
mysql> EXIT;
```

### 4️⃣ Verificar Instalación

1. Abre el navegador
2. Navega a: **http://localhost/equimac**
3. Deberías ver la página principal con productos de ejemplo

---

## 💾 Configuración de Conexión

El archivo `config/database.php` contiene la configuración:

```php
define('DB_HOST', 'localhost');      // Servidor MySQL
define('DB_USER', 'root');           // Usuario (XAMPP default)
define('DB_PASSWORD', '');           // Contraseña (vacía en XAMPP default)
define('DB_NAME', 'equimac');        // Nombre de BD
define('DB_PORT', 3306);             // Puerto MySQL
```

**⚠️ Nota:** En XAMPP, la contraseña por defecto es vacía. Para producción, cambia estas credenciales.

---

## 📖 Cómo Usar el Sistema

### 🔍 Listar Productos
- Accede a **http://localhost/equimac**
- Ver todos los productos en la tabla
- Usa el buscador para filtrar por código, nombre o categoría

### ➕ Crear Nuevo Producto
1. Haz clic en **"+ Nuevo Producto"** (navbar o tabla)
2. Completa el formulario:
   - **Código**: Identificador único (ej: EQ-001)
   - **Nombre**: Descripción del producto
   - **Descripción**: Detalles adicionales
   - **Cantidad**: Stock disponible
   - **Precio**: Costo unitario
   - **Categoría**: Tipo de producto
   - **Estado**: Activo/Inactivo/Descontinuado
3. Haz clic en **"Crear Producto"**

### ✏️ Editar Producto
1. En la tabla, busca el producto
2. Haz clic en **"✎ Editar"**
3. Modifica los datos (el código no se puede cambiar)
4. Haz clic en **"Guardar Cambios"**

### 🗑️ Eliminar Producto
1. En la tabla, busca el producto
2. Haz clic en **"🗑 Eliminar"**
3. Confirma la eliminación en el cuadro de diálogo

### 🔎 Buscar Productos
1. Usa el buscador en la parte superior
2. Escribe código, nombre o categoría
3. Los resultados se filtran automáticamente

---

## 🗂️ Explicación de la Arquitectura MVC

### **Model** (Modelo) - `models/Inventario.php`
- Gestiona la comunicación con la BD
- Contiene métodos CRUD
- Usa Prepared Statements (seguridad)
- No contiene lógica de presentación

```php
// Ejemplo:
$modelo = new Inventario();
$productos = $modelo->obtenerTodos();
```

### **View** (Vista) - `views/*.php`
- Contiene HTML y CSS
- Recibe datos del controlador
- Responsable solo de la presentación
- No contiene lógica de negocio

```php
<!-- Ejemplo:
<?php foreach ($productos as $producto): ?>
    <tr>
        <td><?php echo $producto['nombre']; ?></td>
    </tr>
<?php endforeach; ?>
-->
```

### **Controller** (Controlador) - `controllers/InventarioController.php`
- Orquesta Model y View
- Procesa solicitudes del usuario
- Valida datos
- Prepara datos para la vista

```php
// Ejemplo:
public function guardar() {
    $validacion = $this->validar(...);
    if ($validacion) {
        $this->modelo->crear($datos);
        $this->renderizar('views/lista.php', $datos);
    }
}
```

### **Flujo Completo**
```
Usuario → index.php → Controller → Model → BD
          ↓
          Model ← datos ← BD
          ↓
          View ← datos ← Controller
          ↓
HTML → Navegador ← Usuario
```

---

## 🛡️ Seguridad

### Medidas Implementadas

1. **Prepared Statements** - Previenen SQL Injection
   ```php
   $stmt = $db->prepare("SELECT * FROM inventario WHERE id = ?");
   $stmt->bind_param('i', $id);
   ```

2. **Validación de Entrada** - Cliente y servidor
3. **Escapado de Salida** - `htmlspecialchars()` en vistas
4. **Conexión con Usuario Limitado** - Usuario 'root' sin acceso externo
5. **Errores Silenciosos** - No mostrar detalles en producción

---

## 🐛 Solución de Problemas

### ❌ "Error: Conexión a base de datos rechazada"

**Solución:**
1. Verifica que MySQL esté iniciado en XAMPP
2. Confirma que `database.sql` fue ejecutado
3. En `config/database.php`, verifica las credenciales

### ❌ "Página en blanco"

**Solución:**
1. Abre la consola del navegador (F12)
2. Busca errores en la pestaña "Console"
3. Revisa `php error_log` en XAMPP
4. Cambia `display_errors` a `1` temporalmente en `index.php`

### ❌ "Tabla no encontrada"

**Solución:**
1. Ve a **phpMyAdmin**
2. Verifica que la BD `equimac` existe
3. Ejecuta nuevamente el contenido de `database.sql`

### ❌ "Caracteres extraños en títulos acentuados"

**Solución:**
1. El charset está configurado a UTF-8 en `database.php`
2. Si persiste, en phpMyAdmin:
   - Haz clic en la BD `equimac`
   - Pestaña "Operaciones" → Cambiar charset a `utf8mb4`

---

## 📊 Ejemplo de Datos

La base de datos incluye 5 productos de ejemplo:

| Código | Nombre | Cantidad | Precio |
|--------|--------|----------|--------|
| EQ-001 | Motor Eléctrico 1HP | 15 | $450.00 |
| EQ-002 | Bomba Hidráulica | 8 | $850.00 |
| EQ-003 | Compresor de Aire | 3 | $2,500.00 |
| EQ-004 | Válvula de Control | 45 | $120.00 |
| EQ-005 | Tubo PVC | 120 | $25.00 |

**Puedes eliminar estos y agregar tus propios productos.**

---

## 🔄 Flujo de Datos CRUD

### CREATE (Crear)
```
Usuario → Formulario → Validar → Modelo.crear() → BD INSERT → Listar
```

### READ (Leer)
```
Usuario → Listar → Modelo.obtenerTodos() → BD SELECT → Vista
```

### UPDATE (Actualizar)
```
Usuario → Editar → Formulario → Validar → Modelo.actualizar() → BD UPDATE → Listar
```

### DELETE (Eliminar)
```
Usuario → Confirmar → Modelo.eliminar() → BD DELETE → Listar
```

---

## 📝 Notas Importantes

1. **El código debe ser único** - El sistema evita duplicados
2. **No se puede editar el código** - Por seguridad referencial
3. **Los datos se validan en servidor** - La validación en cliente es extra
4. **Backup regular** - Haz backup de `database.sql` periódicamente
5. **Producción** - Para producción, cambia:
   - Credenciales de BD
   - Ubicación de archivos sensibles fuera de web root
   - Usar HTTPS

---

## 🚀 Mejoras Futuras

Puedes expandir este sistema con:

- [ ] Autenticación de usuarios
- [ ] Historial de movimientos
- [ ] Reportes y gráficos
- [ ] Notificaciones de bajo stock
- [ ] Sistema de permisos
- [ ] API REST
- [ ] Integración con facturación
- [ ] Exportación a PDF/Excel
- [ ] Código de barras

---

## 📞 Soporte

Si encuentras problemas:

1. Revisa los logs en `C:\xampp\apache\logs\error.log`
2. Verifica la pestaña "Network" en DevTools (F12)
3. Consulta la consola del navegador para errores JavaScript
4. Abre una issue en el repositorio

---

## 📄 Licencia

Este proyecto es de código abierto y disponible para uso educativo y comercial.

---

## 👨‍💻 Autor

Sistema desarrollado como ejemplo de **Arquitectura MVC** en PHP.

**Última actualización:** 21 de Abril de 2026

---

## ✅ Checklist de Verificación

Antes de usar en producción:

- [ ] Base de datos creada y poblada
- [ ] Apache y MySQL iniciados
- [ ] Acceso a `http://localhost/equimac` funciona
- [ ] Puedes crear productos
- [ ] Puedes editar productos
- [ ] Puedes eliminar productos
- [ ] Búsqueda funciona
- [ ] Validaciones funcionan
- [ ] Responsive en móvil
- [ ] No hay errores en consola (F12)

---

**¡Bienvenido a EQUIMAC! 🚀**
