<?php

use App\Core\Env;
use App\Core\Router;
use App\Core\Request;
use App\Core\Response;

// Bật CORS cho toàn bộ App
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

require_once __DIR__ . '/../vendor/autoload.php';

// Load biến môi trường
if (file_exists(__DIR__ . '/../.env')) {
    Env::load(__DIR__ . '/../.env');
}

// Fix cho PHP Built-in Server: Nếu là file vật lý thì trả về file đó
$uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . $uriPath)) {
    return false;
}

// Chuẩn hóa path
$path = rtrim($uriPath, '/');
if ($path === '') $path = '/';

// ============================================================
// AUTOMATIC REDIRECTS FOR LEGACY .php URLS
// ============================================================
if (strpos($path, '.php') !== false) {
    $redirectUrl = '';
    
    // Dynamic mappings
    if ($path === '/course-detail.php' && isset($_GET['id'])) {
        $redirectUrl = '/course/' . $_GET['id'];
    } elseif ($path === '/student/learning.php' && isset($_GET['course_id'])) {
        $redirectUrl = '/student/learning/' . $_GET['course_id'];
    } elseif ($path === '/student/course-payment.php' && isset($_GET['course_id'])) {
        $redirectUrl = '/student/payment/' . $_GET['course_id'];
    } elseif ($path === '/admin/preview-course.php' && isset($_GET['id'])) {
        $redirectUrl = '/admin/preview/' . $_GET['id'];
    } else {
        // Static mappings
        $cleanMappings = [
            '/login.php'                => '/login',
            '/register.php'             => '/register',
            '/logout.php'               => '/logout',
            '/forgot-password.php'      => '/forgot-password',
            '/about.php'                => '/about',
            '/courses.php'              => '/courses',
            '/course-detail.php'        => '/courses', // Fallback
            '/profile.php'              => '/profile',
            '/certificate/verify.php'   => '/certificate/verify',
            '/student/dashboard.php'    => '/student/dashboard',
            '/student/my-courses.php'   => '/student/courses',
            '/student/ai-chat.php'      => '/student/ai-chat',
            '/student/chat.php'         => '/student/chat',
            '/student/certificates.php' => '/student/certificates',
            '/teacher/dashboard.php'    => '/teacher/dashboard',
            '/teacher/courses.php'      => '/teacher/courses',
            '/teacher/create-course.php' => '/teacher/create-course',
            '/teacher/course-builder.php' => '/teacher/course-builder',
            '/teacher/students.php'     => '/teacher/students',
            '/teacher/chat.php'         => '/teacher/chat',
            '/admin/dashboard.php'      => '/admin/dashboard',
            '/admin/users.php'          => '/admin/users',
            '/admin/courses.php'        => '/admin/courses',
            '/admin/logs.php'           => '/admin/logs',
            '/admin/vip.php'            => '/admin/vip',
            '/admin/preview-course.php' => '/admin/preview',
        ];
        if (isset($cleanMappings[$path])) {
            $redirectUrl = $cleanMappings[$path];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $redirectUrl .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
    }
    
    if ($redirectUrl !== '') {
        header('Location: ' . $redirectUrl, true, 301);
        exit;
    }
}

// ============================================================
// STATIC ROUTE MAP — clean URL → file PHP
// ============================================================
$staticRoutes = [
    '/'                     => 'home.php',
    '/login'                => 'login.php',
    '/register'             => 'register.php',
    '/logout'               => 'logout.php',
    '/forgot-password'      => 'forgot-password.php',
    '/about'                => 'about.php',
    '/courses'              => 'courses.php',
    '/course-detail'        => 'course-detail.php',
    '/profile'              => 'profile.php',
    '/certificate/verify'   => 'certificate/verify.php',
    // Student
    '/student/dashboard'    => 'student/dashboard.php',
    '/student/courses'      => 'student/my-courses.php',
    '/student/ai-chat'      => 'student/ai-chat.php',
    '/student/chat'         => 'student/chat.php',
    '/student/certificates' => 'student/certificates.php',
    // Teacher
    '/teacher/dashboard'    => 'teacher/dashboard.php',
    '/teacher/courses'      => 'teacher/courses.php',
    '/teacher/create-course' => 'teacher/create-course.php',
    '/teacher/course-builder' => 'teacher/course-builder.php',
    '/teacher/students'     => 'teacher/students.php',
    '/teacher/chat'         => 'teacher/chat.php',
    // Admin
    '/admin/dashboard'      => 'admin/dashboard.php',
    '/admin/users'          => 'admin/users.php',
    '/admin/courses'        => 'admin/courses.php',
    '/admin/logs'           => 'admin/logs.php',
    '/admin/vip'            => 'admin/vip.php',
    '/admin/preview'        => 'admin/preview-course.php',
];

// ============================================================
// DYNAMIC ROUTE MAP — regex → [file, param names]
// ============================================================
$dynamicRoutes = [
    // /course/8  or  /course/lap-trinh-python
    '#^/course/([^/]+)$#'                   => ['file' => 'course-detail.php',          'params' => ['id']],
    // /student/learning/8
    '#^/student/learning/([0-9]+)$#'        => ['file' => 'student/learning.php',       'params' => ['course_id']],
    // /student/payment/8
    '#^/student/payment/([0-9]+)$#'         => ['file' => 'student/course-payment.php', 'params' => ['course_id']],
    // /teacher/course-builder/8
    '#^/teacher/course-builder/([0-9]+)$#'  => ['file' => 'teacher/course-builder.php', 'params' => ['course_id']],
    // /admin/preview/8
    '#^/admin/preview/([0-9]+)$#'           => ['file' => 'admin/preview-course.php',   'params' => ['id']],
];

// ============================================================
// BACKWARD COMPAT: serve legacy .php paths directly
// (so old bookmarks / hardcoded links still work)
// ============================================================
if (!in_array($path, array_keys($staticRoutes)) && strpos($path, '/api/') !== 0) {
    $phpFile = __DIR__ . $path . '.php';
    if (file_exists($phpFile)) {
        header('Content-Type: text/html; charset=utf-8');
        require $phpFile;
        exit;
    }
}

// ── Try static routes ────────────────────────────────────────
if (isset($staticRoutes[$path]) && strpos($path, '/api/') !== 0) {
    $target = __DIR__ . '/' . $staticRoutes[$path];
    if (file_exists($target)) {
        header('Content-Type: text/html; charset=utf-8');
        require $target;
        exit;
    }
}

// ── Try dynamic routes ───────────────────────────────────────
if (strpos($path, '/api/') !== 0) {
    foreach ($dynamicRoutes as $pattern => $config) {
        if (preg_match($pattern, $path, $matches)) {
            // Inject matched params into $_GET so JS can read them via PHP-injected JS vars
            foreach ($config['params'] as $i => $paramName) {
                if (isset($matches[$i + 1])) {
                    $_GET[$paramName] = $matches[$i + 1];
                }
            }
            $target = __DIR__ . '/' . $config['file'];
            if (file_exists($target)) {
                header('Content-Type: text/html; charset=utf-8');
                // Expose route params as a PHP global for pages to use
                $GLOBALS['_ROUTE_PARAMS'] = $_GET;
                require $target;
                exit;
            }
        }
    }
}

// ============================================================
// API ROUTES — handled by core Router
// ============================================================
$request  = new Request();
$response = new Response();
$router   = new Router();

$apiRoutesPath = __DIR__ . '/../routes/api.php';
if (file_exists($apiRoutesPath)) {
    require_once $apiRoutesPath;
} else {
    $response->error('Missing api routes configuration', 500);
}

error_log("API Request: " . $_SERVER['REQUEST_METHOD'] . " " . $uriPath);
$router->dispatch($request, $response);
