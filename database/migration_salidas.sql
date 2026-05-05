-- Ejecutar una vez en BD existente (si ya tenías equimac sin esta tabla).
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
