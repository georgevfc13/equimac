-- Mejoras: órdenes de entrada/salida, múltiples productos por celda (sin cambio de esquema en inventario)
-- Ejecutar una vez en la BD existente.

CREATE TABLE IF NOT EXISTS ordenes_entrada (
  id INT AUTO_INCREMENT PRIMARY KEY,
  numero INT NOT NULL UNIQUE COMMENT 'Secuencia desde 0 → 000, 001…',
  quien_entrego VARCHAR(160) NOT NULL DEFAULT '',
  quien_recibio VARCHAR(160) NOT NULL DEFAULT '',
  observaciones TEXT DEFAULT NULL,
  fecha_entrada DATE DEFAULT NULL,
  hora_entrada TIME DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_oe_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ordenes_salida (
  id INT AUTO_INCREMENT PRIMARY KEY,
  numero INT NOT NULL UNIQUE COMMENT 'Secuencia desde 0 → 000, 001…',
  quien_recibio VARCHAR(160) NOT NULL DEFAULT '',
  quien_entrego VARCHAR(160) NOT NULL DEFAULT '',
  observaciones TEXT DEFAULT NULL,
  fecha_salida DATE DEFAULT NULL,
  hora_salida TIME DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_os_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Columnas en líneas de movimiento (ignorar error si ya existen)
ALTER TABLE entradas ADD COLUMN orden_entrada_id INT NULL;
ALTER TABLE salidas ADD COLUMN orden_salida_id INT NULL;

ALTER TABLE entradas ADD INDEX idx_entradas_orden (orden_entrada_id);
ALTER TABLE salidas ADD INDEX idx_salidas_orden (orden_salida_id);
