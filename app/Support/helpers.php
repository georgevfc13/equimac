<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function base_path(string $path = ''): string
{
    $root = dirname(__DIR__, 2);
    return $path ? $root . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $root;
}

function view(string $name, array $data = []): string
{
    $file = base_path('app/Views/' . str_replace(['..', '\\'], ['', '/'], $name) . '.php');
    if (!is_file($file)) {
        throw new RuntimeException("Vista no encontrada: {$name}");
    }

    extract($data, EXTR_SKIP);
    ob_start();
    require $file;
    return (string)ob_get_clean();
}

function base_url(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $script = (string)($_SERVER['SCRIPT_NAME'] ?? '');
    // Typically: /equimac/public/index.php  (even when URL is /equimac/inventario via rewrite)
    $dir = rtrim(str_replace('\\', '/', dirname($script)), '/');

    // Prefer /equimac (hide /public) when running with root .htaccess rewrite
    if (str_ends_with($dir, '/public')) {
        $dir = rtrim(dirname($dir), '/');
    }

    $base = $dir === '' ? '' : $dir;
    return $base;
}

function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    $base = base_url();
    if ($path === '') {
        return $base !== '' ? $base : '/';
    }
    return ($base !== '' ? $base : '') . '/' . $path;
}

function redirect(string $to, int $status = 302): void
{
    // Allow passing relative paths like "/inventario" or "inventario"
    if ($to !== '' && $to[0] === '/') {
        $to = url(ltrim($to, '/'));
    } else {
        $to = url($to);
    }
    header("Location: {$to}", true, $status);
    exit;
}

function json(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

