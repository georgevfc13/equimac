<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'equimac');
define('DB_PORT', 3306);

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

            if ($this->connection->connect_error) {
                throw new Exception('Error en conexión: ' . $this->connection->connect_error);
            }

            $this->connection->set_charset($this->charset);
        } catch (Exception $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function __clone() {}
    public function __wakeup() {}
}

function getDB() {
    return Database::getInstance()->getConnection();
}
?>
