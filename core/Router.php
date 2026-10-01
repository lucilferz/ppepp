<?php
/**
 * Router - URL Routing Sederhana
 * Platform PPEPP Fakultas
 */

class Router
{
    private array $routes = [];
    private string $basePath = '';

    public function __construct(string $basePath = '')
    {
        $this->basePath = $basePath;
    }

    /**
     * Daftarkan route GET
     */
    public function get(string $path, string $controller, string $method): void
    {
        $this->routes[] = [
            'method' => 'GET',
            'path'   => $path,
            'controller' => $controller,
            'action'     => $method,
        ];
    }

    /**
     * Daftarkan route POST
     */
    public function post(string $path, string $controller, string $method): void
    {
        $this->routes[] = [
            'method' => 'POST',
            'path'   => $path,
            'controller' => $controller,
            'action'     => $method,
        ];
    }

    /**
     * Jalankan routing
     */
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri    = $_SERVER['REQUEST_URI'];

        // Bersihkan base path dan query string
        $path = parse_url($requestUri, PHP_URL_PATH);
        
        // Bersihkan base path folder Laragon (/ppepp/public atau /ppepp) dari awal path
        if (preg_match('#^/ppepp/public#i', $path)) {
            $path = preg_replace('#^/ppepp/public#i', '', $path);
        } elseif (preg_match('#^/ppepp/(ppepp|penetapan|pelaksanaan|evaluasi|pengendalian|peningkatan|kriteria|admin|users|prodi|setting|laporan|auth|dashboard)#i', $path)) {
            $path = preg_replace('#^/ppepp#i', '', $path);
        }
        $path = '/' . trim($path, '/');
        if ($path === '') $path = '/';

        foreach ($this->routes as $route) {
            // Konversi parameter :param ke regex
            $pattern = preg_replace('/\/:([a-zA-Z_]+)/', '/(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if ($route['method'] === $requestMethod && preg_match($pattern, $path, $matches)) {
                // Ambil params
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Load controller
                $controllerFile = BASE_PATH . '/app/controllers/' . $route['controller'] . '.php';
                if (!file_exists($controllerFile)) {
                    $this->notFound("Controller {$route['controller']} tidak ditemukan.");
                    return;
                }

                require_once $controllerFile;
                $controller = new $route['controller']();
                $action     = $route['action'];

                if (!method_exists($controller, $action)) {
                    $this->notFound("Method {$action} tidak ditemukan.");
                    return;
                }

                call_user_func_array([$controller, $action], $params);
                return;
            }
        }

        $this->notFound("Halaman tidak ditemukan: {$path}");
    }

    private function notFound(string $message = '404 Not Found'): void
    {
        http_response_code(404);
        echo "<h1>404 - Halaman Tidak Ditemukan</h1><p>{$message}</p>";
    }
}
