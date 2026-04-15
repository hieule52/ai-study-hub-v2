<?php

/**
 * @var App\Core\Router $router
 */

// ----- Public Routes ----- //
$router->get('/', function($request, $response) {
    $response->success('Welcome to AI Study Hub LMS API V2');
});

$router->get('/api/health', function($request, $response) {
    $response->success('System is running healthy!', ['time' => time()]);
});

// Auth Routes
$router->post('/api/auth/register', 'Api\AuthController@register');
$router->post('/api/auth/login', 'Api\AuthController@login');

// Course Routes
$router->get('/api/courses', 'Api\CourseController@index');
$router->post('/api/courses', 'Api\CourseController@store');
$router->get('/api/courses/:id', 'Api\CourseController@show');

// Lesson Routes
$router->get('/api/courses/:id/curriculum', 'Api\LessonController@curriculum');
$router->get('/api/lessons/:id', 'Api\LessonController@show');
$router->post('/api/lessons/:id/complete', 'Api\LessonController@complete');

// Quiz Routes
$router->get('/api/lessons/:id/quiz', 'Api\QuizController@showByLesson');
$router->post('/api/quizzes/:id/submit', 'Api\QuizController@submit');

// AI Chat Integration
$router->post('/api/ai/chat', 'Api\AiController@chat');

// VIP Payment
$router->post('/api/vip/create-payment', 'Api\VipPaymentController@createPayment');
$router->put('/api/vip/mock-success/:id', 'Api\VipPaymentController@mockSuccess'); // Sandbox Only

// ... More routes will be dynamically added as we build components
