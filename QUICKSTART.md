# ⚡ INICIO RÁPIDO

## 🎯 5 Pasos para Comenzar

### 1. Inicia XAMPP
```bash
Abre XAMPP Control Panel
Click en "Start" para Apache y MySQL
```

### 2. Crea la BD
```
Abre: http://localhost/phpmyadmin
Copia el contenido de database.sql
Pégalo en SQL y ejecuta
```

### 3. Accede al Sistema
```
http://localhost/equimac
```

### 4. Explora
- ➕ Crea un producto
- 🔍 Busca productos
- ✏️ Edita un producto
- 🗑️ Elimina un producto

### 5. ¡Listo! 🎉

---

## 📁 Estructura Esencial

```
equimac/
├── config/        → Conexión a BD
├── models/        → Acceso a datos (CRUD)
├── controllers/   → Lógica del negocio
├── views/         → Interfaces HTML
├── public/        → CSS y JavaScript
└── index.php      → Punto de entrada
```

---

## 🔗 URLs del Sistema

| URL | Función |
|-----|---------|
| `/equimac/` | Listar productos |
| `/equimac/?accion=formulario` | Crear producto |
| `/equimac/?accion=formulario&id=1` | Editar producto |
| `/equimac/?accion=eliminar&id=1` | Eliminar producto |

---

## 💡 Comandos Útiles

### Reiniciar MySQL
```bash
C:\xampp\mysql\bin\mysql -u root < C:\xampp\htdocs\equimac\database.sql
```

### Ver conexión
```php
// En PHP:
$db = getDB();
echo $db->server_info;
```

### Exportar BD
```bash
C:\xampp\mysql\bin\mysqldump -u root equimac > backup.sql
```

---

## ✅ Verificar Instalación

```bash
# Abre navegador y verifica:
http://localhost/equimac        ✓ Conecta a BD
http://localhost/phpmyadmin     ✓ BD creada
```

---

## 🆘 Problemas Comunes

| Problema | Solución |
|----------|----------|
| Página en blanco | Ver error en DevTools (F12) |
| No conecta a BD | Verifica que MySQL está iniciado |
| Caracteres extraños | Charset está en UTF-8 |
| Tabla vacía | Ejecuta database.sql en phpMyAdmin |

---

## 🎓 Flujo MVC (Rápido)

1. **Usuario** → Hace clic en botón
2. **index.php** → Identifica acción
3. **Controller** → Procesa solicitud
4. **Model** → Accede a BD
5. **View** → Renderiza HTML
6. **Navegador** → Muestra resultado

---

**¿Necesitas más ayuda? Ver README.md completo.**
