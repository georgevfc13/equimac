<?php
declare(strict_types=1);

namespace App\Support;

final class Router
{
    /** @var array<string, array<int, array{pattern:string, keys:array<int,string>, handler:callable|array}>> */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, callable|array $handler): void
    {
        $keys = [];
        $pattern = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', function ($m) use (&$keys) {
            $keys[] = $m[1];
            return '([^/]+)';
        }, $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[$method][] = [
            'pattern' => $pattern,
            'keys' => $keys,
            'handler' => $handler,
        ];
    }

    public function dispatch(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Allow running in /equimac/public or /equimac
        // Strip leading project folder and optional /public.
        $path = $this->normalize($path);

        foreach ($this->routes[$method] ?? [] as $route) {
            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }

            array_shift($matches);
            $params = [];
            foreach ($route['keys'] as $i => $key) {
                $params[$key] = $matches[$i] ?? null;
            }

            $handler = $route['handler'];
            if (is_array($handler) && is_string($handler[0])) {
                $class = $handler[0];
                $methodName = $handler[1];
                $instance = new $class();
                $result = $instance->$methodName(...array_values($params));
            } else {
                $result = $handler(...array_values($params));
            }

            if ($result instanceof Response) {
                $result->send();
            }

            if (is_string($result)) {
                Response::html($result)->send();
            }

            // Controller already handled output.
            exit;
        }

        Response::html(view('error/404', ['title' => 'No encontrado']), 404)->send();
    }

    private function normalize(string $path): string
    {
        $path = rtrim($path, '/') ?: '/';

        // Remove project directory prefix if present: /equimac or /equimac/public
        // We detect it by looking for "/equimac" segment.
        $parts = explode('/', ltrim($path, '/'));
        if (count($parts) >= 1 && strtolower($parts[0]) === 'equimac') {
            array_shift($parts);
            if (count($parts) >= 1 && strtolower($parts[0]) === 'public') {
                array_shift($parts);
            }
            $path = '/' . implode('/', $parts);
            $path = rtrim($path, '/') ?: '/';
        }

        return $path;
    }
}

