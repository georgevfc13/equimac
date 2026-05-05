-- EQUIMAC local schema (MySQL/MariaDB)
-- Create database manually if needed:
--   CREATE DATABASE equimac CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS estantes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  numero INT NOT NULL UNIQUE,
  descripcion VARCHAR(255) DEFAULT NULL,
  ubicacion VARCHAR(255) DEFAULT NULL,
  filas INT NOT NULL DEFAULT 5,
  columnas INT NOT NULL DEFAULT 5,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS inventario (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(80) NOT NULL UNIQUE,
  descripcion TEXT NOT NULL,
  unidad VARCHAR(60) NOT NULL,
  cantidad INT NOT NULL DEFAULT 0,

  marca VARCHAR(120) DEFAULT NULL,
  equipo VARCHAR(160) DEFAULT NULL,
  aplicacion VARCHAR(160) DEFAULT NULL,
  tipo_maquinaria VARCHAR(160) DEFAULT NULL,

  estante INT NOT NULL,
  entrepaño INT NOT NULL,
  posicion INT NOT NULL,

  estado VARCHAR(60) DEFAULT NULL,
  de_quien_llego VARCHAR(160) DEFAULT NULL,
  precio_pagado DECIMAL(12,2) DEFAULT NULL,
  quien_recibio VARCHAR(160) DEFAULT NULL,

  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  INDEX idx_ubicacion (estante, entrepaño, posicion),
  INDEX idx_busqueda (codigo, marca, equipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed (optional)
INSERT INTO estantes (numero, descripcion, ubicacion, filas, columnas)
VALUES
  (1, 'Herramientas y consumibles', 'Bodega', 5, 5),
  (2, 'Refacciones', 'Bodega', 5, 5)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

CREATE TABLE IF NOT EXISTS salidas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  inventario_id INT NOT NULL,
  codigo VARCHAR(80) NOT NULL,
  quien_recibio VARCHAR(160) NOT NULL,
  quien_entrego VARCHAR(160) NOT NULL,
  cantidad_usada INT NOT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_salidas_inventario (inventario_id),
  INDEX idx_salidas_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Registro de salidas de material (auditoría + descuento de inventario en la app)
CREATE TABLE IF NOT EXISTS salidas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  inventario_id INT NOT NULL,
  codigo VARCHAR(80) NOT NULL,
  quien_recibio VARCHAR(160) NOT NULL,
  quien_entrego VARCHAR(160) NOT NULL,
  cantidad_usada INT NOT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_inventario (inventario_id),
  INDEX idx_codigo (codigo),
  INDEX idx_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

