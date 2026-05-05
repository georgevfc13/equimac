# EQUIMAC (Localhost)

App de inventario **solo local** pensada para correr en **XAMPP / localhost**.

## Requisitos

- Apache + PHP (XAMPP)
- MySQL/MariaDB

## Instalación rápida

1) Crea la base de datos `equimac` en phpMyAdmin.

2) Importa el archivo `database/schema.sql`.

3) Abre en el navegador:

- Recomendado (XAMPP por defecto en `htdocs/`):
  - `http://localhost/equimac/inventario`

- Alternativa (si entras directo a `public/`):
  - `http://localhost/equimac/public/inventario`

## Configuración DB (opcional)

Por defecto usa:

- host: `127.0.0.1`
- puerto: `3306`
- user: `root`
- pass: *(vacío)*
- db: `equimac`

Puedes sobre-escribir con variables de entorno:

- `EQUIMAC_DB_HOST`
- `EQUIMAC_DB_PORT`
- `EQUIMAC_DB_NAME`
- `EQUIMAC_DB_USER`
- `EQUIMAC_DB_PASS`

