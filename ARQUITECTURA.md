# 🏗️ DOCUMENTACIÓN DE ARQUITECTURA MVC

## 1. Visión General

El Sistema de Inventario EQUIMAC sigue el patrón **MVC (Model-View-Controller)** de manera estricta, garantizando:

- ✅ Separación clara de responsabilidades
- ✅ Código mantenible y escalable
- ✅ Fácil testing y debugging
- ✅ Reutilización de componentes

---

## 2. Diagrama de Flujo

```
┌─────────────────────────────────────────────────────┐
│                  NAVEGADOR WEB                       │
└────────────────────┬────────────────────────────────┘
                     │ HTTP Request
                     ▼
┌─────────────────────────────────────────────────────┐
│                  INDEX.PHP                           │
│         (Enrutador - Punto de Entrada)              │
│  • Identifica acción (listar, guardar, eliminar)    │
│  • Instancia controlador                            │
└────────────────────┬────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────┐
│            CONTROLLER                               │
│    (InventarioController.php)                       │
│  • listar() → Obtiene productos                     │
│  • formulario() → Muestra formulario                │
│  • guardar() → Valida y guarda                      │
│  • eliminar() → Elimina producto                    │
└────────────┬────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────┐
│              MODEL                                  │
│      (Inventario.php)                               │
│  • crear($datos)                                   │
│  • obtenerTodos($filtro)                           │
│  • obtenerPorId($id)                               │
│  • actualizar($id, $datos)                         │
│  • eliminar($id)                                   │
└────────────┬───────────────────┬────────────────────┘
             │                   │
             ▼                   ▼
┌──────────────────┐   ┌──────────────────┐
│  DATABASE.PHP    │   │    BASE DE DATOS │
│  (Configuración) │   │    (MySQL)       │
│  • DSN           │   │  • inventario    │
│  • Conexión      │   │  • Datos         │
│  • Singleton     │   │  • Índices       │
└──────────────────┘   └──────────────────┘
             ▲                   ▲
             │ mysqli            │ SQL
             └───────────────────┘
                     │
                     ▼
          Retorna datos/resultados
                     │
             ┌───────┴───────┐
             ▼               ▼
    ┌──────────────┐  ┌──────────────┐
    │  Controller  │  │ Renderiza    │
    │  Prepara     │  │ Vista        │
    │  datos       │  │              │
    └──────┬───────┘  └──────┬───────┘
           │                 │
           └────────┬────────┘
                    ▼
         ┌──────────────────────┐
         │      VIEW            │
         │  (lista.php,         │
         │   formulario.php)    │
         │  • HTML              │
         │  • CSS               │
         │  • Presentación      │
         └──────────┬───────────┘
                    │
                    ▼ HTML Response
         ┌──────────────────────┐
         │  NAVEGADOR WEB       │
         │  Renderiza HTML      │
         └──────────────────────┘
```

---

## 3. Componentes Principales

### 3.1 MODEL (Modelo)

**Archivo:** `models/Inventario.php`

**Responsabilidades:**
- Acceso directo a la base de datos
- Lógica de consultas
- Validaciones de integridad de datos

**Métodos:**
```php
public function crear($datos)              // INSERT
public function obtenerTodos($filtro)      // SELECT *
public function obtenerPorId($id)          // SELECT WHERE id
public function actualizar($id, $datos)    // UPDATE
public function eliminar($id)              // DELETE
public function obtenerCategorias()        // SELECT DISTINCT
public function obtenerEstadisticas()      // Aggregates
public function codigoExiste($codigo)      // Validaciones
```

**Características de Seguridad:**
- ✅ Prepared Statements
- ✅ Type Binding
- ✅ Input Validation

```php
// Ejemplo seguro:
$sql = "INSERT INTO inventario (codigo, nombre) VALUES (?, ?)";
$stmt = $db->prepare($sql);
$stmt->bind_param('ss', $codigo, $nombre);
$stmt->execute();
```

---

### 3.2 CONTROLLER (Controlador)

**Archivo:** `controllers/InventarioController.php`

**Responsabilidades:**
- Procesar solicitudes HTTP
- Validar entrada del usuario
- Coordinar Model y View
- Preparar datos para la Vista

**Métodos:**
```php
public function listar()                // GET - Listar productos
public function formulario()            // GET - Mostrar formulario
public function guardar()               // POST - Crear/Actualizar
public function eliminar()              // GET - Eliminar

private function validar()              // Validar datos
private function renderizar()           // Renderizar vista
```

**Flujo de Guardado:**
```php
public function guardar() {
    1. Verificar método POST
    2. Obtener datos de $_POST
    3. Validar (cliente + servidor)
    4. Verificar códigos únicos
    5. Llamar Model::crear() o Model::actualizar()
    6. Renderizar vista con mensaje
}
```

---

### 3.3 VIEW (Vista)

**Archivos:**
- `views/layout.php` - Layout base HTML
- `views/lista.php` - Tabla de productos
- `views/formulario.php` - Formulario CRUD

**Responsabilidades:**
- Solo presentación (HTML/CSS)
- Recibe datos del Controller
- Sin lógica de negocio

**Estructura:**
```php
<!-- Dentro de la vista -->
<?php foreach ($productos as $producto): ?>
    <tr>
        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
    </tr>
<?php endforeach; ?>
```

---

### 3.4 CONFIG (Configuración)

**Archivo:** `config/database.php`

**Responsabilidades:**
- Conectar a la BD
- Proporcionar instancia única (Singleton)
- Definir credenciales

**Patrón Singleton:**
```php
private static $instance = null;

public static function getInstance() {
    if (self::$instance === null) {
        self::$instance = new Database();
    }
    return self::$instance;
}
```

**Beneficios:**
- ✅ Una sola conexión por aplicación
- ✅ Mejor rendimiento
- ✅ Evita conexiones múltiples

---

## 4. Flujo Detallado de una Acción

### Ejemplo: Crear Producto

```
1. USUARIO
   └─ Completa formulario y hace clic en "Crear Producto"

2. NAVEGADOR
   └─ Envía POST a index.php?accion=guardar

3. INDEX.PHP
   └─ Identifica acción "guardar"
   └─ Instancia InventarioController

4. CONTROLLER::guardar()
   ├─ Verifica método POST
   ├─ Obtiene datos de $_POST
   ├─ Valida (length, tipos, no vacíos)
   ├─ Verifica código único via Model::codigoExiste()
   └─ Si todo es válido:
       ├─ Llama Model::crear($datos)
       └─ Renderiza vista/lista.php con mensaje "Exito"

5. MODEL::crear()
   ├─ Prepara consulta INSERT
   ├─ Hace bind de parámetros
   ├─ Ejecuta query
   └─ Retorna resultado

6. DATABASE
   └─ Inserta registro en inventario

7. CONTROLLER retorna
   └─ Renderiza vista/lista.php

8. VIEW::lista.php
   ├─ Recibe productos actualizados
   ├─ Renderiza tabla HTML
   └─ Genera HTML con CSS

9. NAVEGADOR
   └─ Muestra página con nuevo producto
```

---

## 5. Tabla de Responsabilidades

| Capa | Archivo | Responsabilidad | NO Hace |
|------|---------|-----------------|---------|
| **Model** | `Inventario.php` | Acceso a BD, queries | HTML, validaciones usuario |
| **Controller** | `InventarioController.php` | Lógica, validación, orquestación | BD directa, HTML directo |
| **View** | `lista.php` | Presentación HTML/CSS | Queries, lógica negocio |
| **Config** | `database.php` | Conexión única | Queries, presentación |

---

## 6. Estructura de Datos

### Base de Datos
```sql
CREATE TABLE inventario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    cantidad INT NOT NULL DEFAULT 0,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    categoria VARCHAR(100),
    estado ENUM('activo', 'inactivo', 'descontinuado'),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

### Estructura de Carpetas
```
equimac/
├── config/           → Configuración
├── models/           → Modelos (Acceso a datos)
├── controllers/      → Controladores (Lógica)
├── views/            → Vistas (Presentación)
├── public/
│   ├── css/          → Estilos
│   └── js/           → Scripts
├── utils.php         → Utilidades
├── index.php         → Punto de entrada
└── database.sql      → Schema
```

---

## 7. Validaciones

### En Cliente (JavaScript)
```javascript
// views/formulario.php
- Campos requeridos
- Cantidad >= 0
- Precio >= 0
- Código no vacío
```

### En Servidor (PHP)
```php
// controllers/InventarioController.php
private function validar($codigo, $nombre, $cantidad, $precio) {
    $errores = [];
    if (empty($codigo)) $errores[] = 'Código requerido';
    if (empty($nombre)) $errores[] = 'Nombre requerido';
    if ($cantidad < 0) $errores[] = 'Cantidad debe ser ≥ 0';
    if ($precio < 0) $errores[] = 'Precio debe ser ≥ 0';
    return $errores;
}
```

---

## 8. Seguridad en Profundidad

```
Usuario Input
    ↓
[JavaScript Validation]  ← Prevenir envíos mal formados
    ↓
[HTTP Request]
    ↓
[PHP Validation]         ← Validar tipo y rango
    ↓
[Sanitización]           ← htmlspecialchars(), trim()
    ↓
[Prepared Statement]     ← Prevenir SQL Injection
    ↓
[Database Execute]
    ↓
[Resultado]
    ↓
[Escape Output]          ← htmlspecialchars() en vistas
    ↓
[HTML Response]
    ↓
[Navegador Renderiza]
```

---

## 9. Patrones Utilizados

### 9.1 Singleton
```php
// Una sola instancia de conexión
$db = Database::getInstance();
```

### 9.2 MVC
```
Separación estricta de responsabilidades
```

### 9.3 CRUD
```
Create, Read, Update, Delete implementados
```

### 9.4 Repository Pattern
```php
// Inventario actúa como repository
public function obtenerTodos()
public function obtenerPorId()
```

---

## 10. Escalabilidad Futura

El sistema puede extenderse con:

```
equimac/
├── utils/                 ← Helpers
├── middleware/            ← Autenticación, logs
├── api/                   ← Endpoints REST
├── migrations/            ← Versionado de BD
├── tests/                 ← Unit tests
├── cache/                 ← Caché
├── uploads/               ← Archivos
└── logs/                  ← Logs del sistema
```

---

## 11. Referencia Rápida

| Acción | Archivo | Método | URL |
|--------|---------|--------|-----|
| Listar | `lista.php` | `listar()` | `?accion=listar` |
| Crear | `formulario.php` | `guardar()` | `?accion=formulario` |
| Editar | `formulario.php` | `guardar()` | `?accion=formulario&id=1` |
| Eliminar | `lista.php` | `eliminar()` | `?accion=eliminar&id=1` |

---

**Fin de la Documentación de Arquitectura**
