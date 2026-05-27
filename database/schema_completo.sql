-- EQUIMAC - SCHEMA COMPLETO
-- Base de datos con tablas de estantes, inventario, entradas y salidas
-- Copiar y pegar todo este código en un editor SQL para ejecutar desde 0


-- ============================================================================
-- TABLA: LA BASE DE DATOS
-- ============================================================================
CREATE DATABASE EQUIMAC;
USE EQUIMAC;
-- ============================================================================
-- TABLA: ESTANTES
-- ============================================================================
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

-- ============================================================================
-- TABLA: INVENTARIO
-- ============================================================================
CREATE TABLE IF NOT EXISTS inventario (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(80) NOT NULL UNIQUE,
  nombre VARCHAR(160) NOT NULL,
  descripcion TEXT NOT NULL,
  unidad VARCHAR(60) NOT NULL,
  cantidad INT NOT NULL DEFAULT 0,
  stock_minimo INT NOT NULL DEFAULT 5,

  marca VARCHAR(120) DEFAULT NULL,
  equipo VARCHAR(160) DEFAULT NULL,
  aplicacion VARCHAR(160) DEFAULT NULL,
  tipo_maquinaria VARCHAR(160) DEFAULT NULL,

  estante INT NOT NULL,
  entrepaño INT NOT NULL,
  posicion INT NOT NULL,

  estado VARCHAR(60) DEFAULT NULL,
  de_quien_llego VARCHAR(160) DEFAULT NULL COMMENT 'De quién llegó (entrada inicial)',
  precio_pagado DECIMAL(12,2) DEFAULT NULL,
  quien_recibio VARCHAR(160) DEFAULT NULL COMMENT 'Quién recibió (entrada inicial)',

  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  INDEX idx_ubicacion (estante, entrepaño, posicion),
  INDEX idx_busqueda (nombre, codigo, marca, equipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- TABLA: ENTRADAS (Historial de entradas de productos)
-- ============================================================================
CREATE TABLE IF NOT EXISTS entradas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  inventario_id INT NOT NULL,
  codigo VARCHAR(80) NOT NULL,
  cantidad INT NOT NULL,
  quien_entrego VARCHAR(160) NOT NULL,
  quien_recibio VARCHAR(160) NOT NULL,
  observaciones TEXT DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_inventario (inventario_id),
  INDEX idx_codigo (codigo),
  INDEX idx_fecha (created_at),
  FOREIGN KEY (inventario_id) REFERENCES inventario(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- TABLA: SALIDAS (Historial de salidas/consumo de productos)
-- ============================================================================
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