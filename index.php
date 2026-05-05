<?php
declare(strict_types=1);

// Fallback entrypoint when Apache ignores .htaccess (shows directory listing).
// Redirect to /public app.

$script = (string)($_SERVER['SCRIPT_NAME'] ?? '/equimac/index.php');
$base = rtrim(str_replace('\\', '/', dirname($script)), '/');
$target = ($base !== '' ? $base : '') . '/public/inventario';

header('Location: ' . $target, true, 302);
exit;

