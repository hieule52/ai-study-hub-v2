<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $uri, $action): void
    {
        // Chuyển đổi các parameter dạng :id sang regex (vd: /api/users/:id -> /api/users/([a-zA-Z0-9_-]+))
        $pattern = preg_replace('/\:([a-zA-Z0-9_]+)/', '([a-zA-Z0-9_-]+)', $uri);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => strtoupper($method),
            'uri' => $uri,
            'pattern' => $pattern,
            'action' => $action
        ];
    }

    public function get(string $uri, $action): void
    {
        $this->add('GET', $uri, $action);
    }

    public function post(string $uri, $action): void
    {
        $this->add('POST', $uri, $action);
    }

    public function put(string $uri, $action): void
    {
        $this->add('PUT', $uri, $action);
    }

    public function delete(string $uri, $action): void
    {
        $this->add('DELETE', $uri, $action);
    }

    public function dispatch(Request $request, Response $response)
    {
        $uri = $request->getUri();
        $method = $request->getMethod();

        // Xử lý Preflight request cho CORS
        if ($method === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
            header('Access-Control-Allow-Headers: Authorization, Content-Type');
            exit(0);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                
                array_shift($matches); // Loại bỏ phần tử match toàn bộ chuỗi
                
                $action = $route['action'];

                // Hỗ trợ Middleware tại đây trong tương lai

                if (is_callable($action)) {
                    return call_user_func_array($action, array_merge([$request, $response], $matches));
                }

                if (is_array($action) && count($action) == 2) {
                    $controller = new $action[0]();
                    $methodToCall = $action[1];
                    return call_user_func_array([$controller, $methodToCall], array_merge([$request, $response], $matches));
                }

                if (is_string($action)) {
                    list($controllerClass, $methodToCall) = explode('@', $action);
                    $fullController = "App\\Controllers\\" . $controllerClass;
                    $controller = new $fullController();
                    
                    return call_user_func_array([$controller, $methodToCall], array_merge([$request, $response], $matches));
                }
            }
        }

        return $response->error('404 Not Found - Endpoint does not exist', 404);
    }
}
