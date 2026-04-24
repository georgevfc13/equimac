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
    posicion INT DEFAULT 1,
    estado VARCHAR(50),
    tipo_maquinaria VARCHAR(100),
    de_quien_llego VARCHAR(255),
    precio_pagado DECIMAL(10, 2),
    quien_recibio VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo),
    INDEX idx_estante_entrepaño (estante, entrepaño),
    INDEX idx_marca (marca),
    INDEX idx_equipo (equipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sentencias ALTER para agregar campos si la tabla ya existe
ALTER TABLE inventario ADD COLUMN IF NOT EXISTS posicion INT DEFAULT 1;
ALTER TABLE inventario ADD COLUMN IF NOT EXISTS de_quien_llego VARCHAR(255);
ALTER TABLE inventario ADD COLUMN IF NOT EXISTS precio_pagado DECIMAL(10, 2);
ALTER TABLE inventario ADD COLUMN IF NOT EXISTS quien_recibio VARCHAR(255);
 
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

-- ============================================
-- DATOS INICIALES - ESTANTES
-- ============================================

INSERT IGNORE INTO estantes (numero, filas, columnas, descripcion, ubicacion) VALUES
(1, 5, 5, 'Estante Principal A', 'Zona 1 - Entrada'),
(2, 5, 5, 'Estante Principal B', 'Zona 1 - Centro'),
(3, 5, 5, 'Estante Principal C', 'Zona 1 - Fondo'),
(4, 4, 4, 'Estante Auxiliar 1', 'Zona 2 - Izquierda'),
(5, 4, 4, 'Estante Auxiliar 2', 'Zona 2 - Derecha');

-- ============================================
-- DATOS INICIALES - PRODUCTOS EJEMPLO
-- ============================================

INSERT IGNORE INTO inventario (codigo, descripcion, unidad, cantidad, marca, equipo, aplicacion, estante, entrepaño, estado, tipo_maquinaria) VALUES
('EQ-001', 'Motor Eléctrico 1HP', 'Pieza', 3, 'WEG', 'Compresor', 'Aire Comprimido', 1, 1, 'Activo', 'Motores'),
('EQ-002', 'Válvula Solenoide 24V', 'Pieza', 15, 'Siemens', 'Sistema Neumático', 'Control de Aire', 1, 1, 'Activo', 'Válvulas'),
('EQ-003', 'Sensor Inductivo M18', 'Pieza', 8, 'Balluff', 'Control de Línea', 'Detección', 1, 2, 'Activo', 'Sensores'),
('EQ-004', 'Cable Armado 4x10mm', 'Metro', 50, 'Condumex', 'Distribución Eléctrica', 'Instalación', 2, 1, 'Activo', 'Cables'),
('EQ-005', 'Contacto Auxiliar 2NA2NF', 'Pieza', 20, 'ABB', 'Contactor', 'Control', 2, 2, 'Activo', 'Accesorios'),
('EQ-006', 'Compresor de Aire Portátil', 'Pieza', 2, 'Campbell Hausfeld', 'Red de Aire', 'Aire Comprimido', 3, 3, 'Mantenimiento', 'Máquinas'),
('EQ-007', 'Filtro para Aire Comprimido', 'Pieza', 12, 'Hydac', 'Sistema Neumático', 'Filtración', 3, 3, 'Activo', 'Accesorios'),
('EQ-008', 'Tubo Neumático PU 8x5mm', 'Metro', 100, 'Parker', 'Sistema Neumático', 'Distribución Neumática', 1, 4, 'Activo', 'Tuberías'),
('EQ-009', 'Cilindro Neumático 50x100', 'Pieza', 4, 'Festo', 'Actuadores', 'Movimiento', 4, 1, 'Activo', 'Actuadores'),
('EQ-010', 'Rodamiento 6305 2Z', 'Pieza', 30, 'SKF', 'Transmisión', 'Soporte Mecánico', 4, 2, 'Activo', 'Rodamientos');
