-- ============================================
-- Base de Datos: EQUIMAC
-- Tabla: INVENTARIO
-- ============================================

CREATE DATABASE IF NOT EXISTS equimac;
USE equimac;

-- Tabla de Inventario
CREATE TABLE IF NOT EXISTS inventario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    cantidad INT NOT NULL DEFAULT 0,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    categoria VARCHAR(100),
    estado ENUM('activo', 'inactivo', 'descontinuado') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices para mejorar búsquedas
CREATE INDEX idx_codigo ON inventario(codigo);
CREATE INDEX idx_nombre ON inventario(nombre);
CREATE INDEX idx_categoria ON inventario(categoria);
CREATE INDEX idx_estado ON inventario(estado);

-- Datos de ejemplo
INSERT INTO inventario (codigo, nombre, descripcion, cantidad, precio_unitario, categoria, estado) VALUES
('EQ-001', 'Motor Eléctrico 1HP', 'Motor eléctrico trifásico 1HP', 15, 450.00, 'Motores', 'activo'),
('EQ-002', 'Bomba Hidráulica', 'Bomba centrífuga 2HP', 8, 850.00, 'Bombas', 'activo'),
('EQ-003', 'Compresor de Aire', 'Compresor de tornillo 5HP', 3, 2500.00, 'Compresores', 'activo'),
('EQ-004', 'Válvula de Control', 'Válvula solenoide 2"', 45, 120.00, 'Válvulas', 'activo'),
('EQ-005', 'Tubo PVC', 'Tubo PVC 2" x 3m', 120, 25.00, 'Tuberías', 'activo');
