<?php
/**
 * Configuración de Conexión a Base de Datos
 * Archivo: config/database.php
 */

// ============================================
// CREDENCIALES LOCALHOST
// ============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');  // XAMPP por defecto no tiene contraseña
define('DB_NAME', 'equimac');
define('DB_PORT', 3306);

// ============================================
// Clase de Conexión (Singleton)
// ============================================
class Database {
    private static $instance = null;
    private $connection;
    private $charset = 'utf8mb4';

    private function __construct() {
        try {
            $this->connection = new mysqli(
                DB_HOST,
                DB_USER,
                DB_PASSWORD,
                DB_NAME,
                DB_PORT
            );

            // Verificar conexión
            if ($this->connection->connect_error) {
                throw new Exception('Error en conexión: ' . $this->connection->connect_error);
            }

            // Establecer charset UTF-8
            $this->connection->set_charset($this->charset);

        } catch (Exception $e) {
            die('Error de conexión a base de datos: ' . $e->getMessage());
        }
    }

    /**
     * Obtener instancia única de la conexión (Patrón Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Obtener la conexión MySQLi
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Prevenir clonación
     */
    private function __clone() {}

    /**
     * Prevenir unserialization
     */
    private function __wakeup() {}
}

/**
 * Función helper para obtener la conexión
 */
function getDB() {
    return Database::getInstance()->getConnection();
}
?>
