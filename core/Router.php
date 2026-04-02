<?php

namespace App\Core;

class Router
{
    private $routes = [
        'GET' => [],
        'POST' => []
    ];

    public function get($uri, $action)
    {
        $this->routes['GET'][$uri] = $action;
        //$this->routes['GET']['/'] = 'HomeController@index';
    }

    public function post($uri, $action)
    {
        $this->routes['POST'][$uri] = $action;
    }

    public function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Nếu chạy trong thư mục public/
        $uri = str_replace('/public', '', $uri); // bỏ public

        if (!isset($this->routes[$method][$uri])) {
            echo "404 Not Found: $uri";
            return;
        }

        // ===== KIỂM TRA AUTHENTICATION =====
        // Danh sách routes công khai (không cần đăng nhập)
        $publicRoutes = ['/', '/about', '/login', '/register', '/logout'];
        
        // Nếu không phải route công khai và chưa đăng nhập
        if (!in_array($uri, $publicRoutes)) {
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
                // Lưu URL để redirect sau khi login
                $_SESSION['redirect_after_login'] = $uri;
                
                // Redirect về login
                header('Location: /login');
                exit;
            }
        }

        // Kiểm tra admin routes
        $adminRoutes = ['/management', '/admin'];
        $isAdminRoute = false;
        foreach ($adminRoutes as $adminRoute) {
            if (strpos($uri, $adminRoute) === 0) { // Kiểm tra xem URI có bắt đầu bằng admin route không
                $isAdminRoute = true;
                break;
            }
        }
        // 0 → tìm thấy ngay đầu chuỗi
        // > 0 → tìm thấy nhưng không ở đầu
        // false → không tìm thấy

        if ($isAdminRoute) {
            if ((int)($_SESSION['is_admin'] ?? 0) !== 1) {
                header('Location: /');
                exit;
            }
        }

        $action = $this->routes[$method][$uri];
        // $action = 'ChatController@chatWithFriend';
        list($controllerName, $methodCall) = explode('@', $action);
        //$controllerName = "ChatController";
        //$methodCall = "chatWithFriend";

        // FULL namespace controller
        $fullController = "App\\Controllers\\$controllerName";

        // Khởi tạo controller nhờ Composer autoload
        $controllerObj = new $fullController();

        return $controllerObj->$methodCall();
        // $controllerObj->chatWithFriend();
    }
}
