<?php
class Utils {
    public static function formatearMoneda($valor) {
        return '$' . number_format($valor, 2, '.', ',');
    }

    public static function formatearFecha($fecha, $formato = 'd/m/Y H:i') {
        return date($formato, strtotime($fecha));
    }

    public static function sanitizar($texto) {
        return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
    }

    public static function validarEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validarURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public static function redirigir($url) {
        header("Location: $url");
        exit;
    }

    public static function obtenerGET($clave, $defecto = null) {
        return isset($_GET[$clave]) ? self::sanitizar($_GET[$clave]) : $defecto;
    }

    public static function obtenerPOST($clave, $defecto = null) {
        return isset($_POST[$clave]) ? self::sanitizar($_POST[$clave]) : $defecto;
    }

    public static function generarID() {
        return uniqid('', true);
    }

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
