<?php
/**
 * Funciones Utilitarias
 * Archivo: utils.php
 */

class Utils {
    
    /**
     * Formatear moneda
     */
    public static function formatearMoneda($valor) {
        return '$' . number_format($valor, 2, '.', ',');
    }

    /**
     * Formatear fecha
     */
    public static function formatearFecha($fecha, $formato = 'd/m/Y H:i') {
        return date($formato, strtotime($fecha));
    }

    /**
     * Sanitizar entrada
     */
    public static function sanitizar($texto) {
        return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validar email
     */
    public static function validarEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validar URL
     */
    public static function validarURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * Redirigir
     */
    public static function redirigir($url) {
        header("Location: $url");
        exit;
    }

    /**
     * Obtener variable GET segura
     */
    public static function obtenerGET($clave, $defecto = null) {
        return isset($_GET[$clave]) ? self::sanitizar($_GET[$clave]) : $defecto;
    }

    /**
     * Obtener variable POST segura
     */
    public static function obtenerPOST($clave, $defecto = null) {
        return isset($_POST[$clave]) ? self::sanitizar($_POST[$clave]) : $defecto;
    }

    /**
     * Generar ID único
     */
    public static function generarID() {
        return uniqid('', true);
    }

    /**
     * Log de actividad
     */
    public static function registrarLog($mensaje, $tipo = 'info') {
        $archivo = __DIR__ . '/logs/sistema.log';
        $directorio = dirname($archivo);
        
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $fecha = date('Y-m-d H:i:s');
        $contenido = "[$fecha] [$tipo] $mensaje" . PHP_EOL;
        file_put_contents($archivo, $contenido, FILE_APPEND);
    }
}
?>
