-- ============================================
-- Base de Datos: EQUIMAC
-- Tabla: INVENTARIO
-- ============================================

CREATE DATABASE IF NOT EXISTS equimac;
USE equimac;

-- Tabla de Inventario
CREATE TABLE IF NOT EXISTS inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NOT NULL,
    unidad VARCHAR(50) NOT NULL,
    cantidad INT NOT NULL DEFAULT 0,
    marca VARCHAR(100),
    equipo VARCHAR(100),
    aplicacion VARCHAR(100),
    estante INT NOT NULL,
    entrepaño INT NOT NULL,
    estado VARCHAR(50),
    tipo_maquinaria VARCHAR(100),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo),
    INDEX idx_estante_entrepaño (estante, entrepaño),
    INDEX idx_marca (marca),
    INDEX idx_equipo (equipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
-- Tabla de Estantes (configuración)
CREATE TABLE IF NOT EXISTS estantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL UNIQUE,
    filas INT NOT NULL DEFAULT 5,
    columnas INT NOT NULL DEFAULT 4,
    descripcion VARCHAR(255),
    ubicacion VARCHAR(100),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
