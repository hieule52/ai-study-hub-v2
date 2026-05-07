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

// User Profile Routes
$router->get('/api/user/profile', 'Api\UserController@profile');
$router->put('/api/user/profile', 'Api\UserController@updateProfile');
$router->put('/api/user/change-password', 'Api\UserController@changePassword');
$router->post('/api/user/avatar', 'Api\UserController@uploadAvatar');

// =============================================
// COURSE ROUTES
// =============================================
$router->get('/api/courses', 'Api\CourseController@index');
$router->post('/api/courses', 'Api\CourseController@store');
$router->get('/api/courses/:id', 'Api\CourseController@show');
$router->post('/api/courses/:id/enroll', 'Api\StudentController@enrollCourse');
$router->post('/api/courses/:id/verify-purchase', 'Api\StudentController@verifyPurchase');

// Course Categories
$router->get('/api/categories', 'Api\CategoryController@index');
$router->post('/api/categories', 'Api\CategoryController@store');
$router->put('/api/categories/:id', 'Api\CategoryController@update');
$router->delete('/api/categories/:id', 'Api\CategoryController@delete');

// =============================================
// STUDENT ROUTES
// =============================================
$router->get('/api/student/courses', 'Api\StudentController@getEnrolledCourses');
$router->get('/api/student/stats', 'Api\StudentController@getStats');

// Lesson Routes
$router->get('/api/courses/:id/curriculum', 'Api\LessonController@curriculum');
$router->get('/api/lessons/:id', 'Api\LessonController@show');
$router->post('/api/lessons/:id/complete', 'Api\LessonController@complete');

// Quiz Routes
$router->get('/api/lessons/:id/quiz', 'Api\QuizController@showByLesson');
$router->post('/api/quizzes/:id/submit', 'Api\QuizController@submit');

// =============================================
// VIDEO STREAMING (Secured)
// =============================================
$router->get('/api/video/token/:lessonId', 'Api\VideoStreamController@getToken');
$router->get('/api/video/stream', 'Api\VideoStreamController@stream');

// =============================================
// LEARNING PATHS (Lộ trình học)
// =============================================
$router->get('/api/learning-paths/my', 'Api\LearningPathController@myPaths');
$router->get('/api/learning-paths', 'Api\LearningPathController@index');
$router->get('/api/learning-paths/:id', 'Api\LearningPathController@show');
$router->post('/api/learning-paths', 'Api\LearningPathController@store');
$router->put('/api/learning-paths/:id', 'Api\LearningPathController@update');
$router->post('/api/learning-paths/:id/courses', 'Api\LearningPathController@addCourse');
$router->delete('/api/learning-paths/:pathId/courses/:courseId', 'Api\LearningPathController@removeCourse');
$router->post('/api/learning-paths/:id/enroll', 'Api\LearningPathController@enroll');
$router->get('/api/learning-paths/:id/progress', 'Api\LearningPathController@progress');

// =============================================
// AI CHAT
// =============================================
$router->post('/api/ai/chat', 'Api\AiController@chat');

// =============================================
// VIP PAYMENT
// =============================================
$router->post('/api/vip/create-payment', 'Api\VipPaymentController@createPayment');
$router->put('/api/vip/mock-success/:id', 'Api\VipPaymentController@mockSuccess'); // Sandbox Only

// =============================================
// TEACHER ROUTES
// =============================================
$router->get('/api/teacher/dashboard', 'Api\TeacherController@dashboard');
$router->put('/api/teacher/courses/:id', 'Api\TeacherController@updateCourse');
$router->delete('/api/teacher/courses/:id', 'Api\TeacherController@deleteCourse');
$router->get('/api/teacher/students', 'Api\TeacherController@students');

// Teacher Curriculum CRUD
$router->post('/api/teacher/chapters', 'Api\TeacherCurriculumController@createChapter');
$router->put('/api/teacher/chapters/:id', 'Api\TeacherCurriculumController@updateChapter');
$router->delete('/api/teacher/chapters/:id', 'Api\TeacherCurriculumController@deleteChapter');
$router->put('/api/teacher/chapters/reorder', 'Api\TeacherCurriculumController@reorderChapters');

$router->post('/api/teacher/lessons', 'Api\TeacherCurriculumController@createLesson');
$router->put('/api/teacher/lessons/:id', 'Api\TeacherCurriculumController@updateLesson');
$router->delete('/api/teacher/lessons/:id', 'Api\TeacherCurriculumController@deleteLesson');
$router->put('/api/teacher/lessons/reorder', 'Api\TeacherCurriculumController@reorderLessons');

$router->post('/api/teacher/quizzes', 'Api\TeacherCurriculumController@createQuiz');
$router->put('/api/teacher/quizzes/:id', 'Api\TeacherCurriculumController@updateQuiz');
$router->delete('/api/teacher/quizzes/:id', 'Api\TeacherCurriculumController@deleteQuiz');

// File Upload (Image / Video)
$router->post('/api/upload', 'Api\UploadController@upload');
$router->post('/api/upload/image', 'Api\UploadController@uploadImage');
$router->post('/api/upload/video', 'Api\UploadController@uploadVideo');

// =============================================
// ADMIN ROUTES
// =============================================
$router->get('/api/admin/chart-data', 'Api\AdminController@getChartData');
$router->get('/api/admin/stats', 'Api\AdminController@getStats');
$router->get('/api/admin/users', 'Api\AdminController@getUsers');
$router->put('/api/admin/users/:id', 'Api\AdminController@updateUser');
$router->delete('/api/admin/users/:id', 'Api\AdminController@deleteUser');
$router->put('/api/admin/users/:id/status', 'Api\AdminController@updateUserStatus');
$router->put('/api/admin/users/:id/role', 'Api\AdminController@updateUserRole');
$router->get('/api/admin/courses/pending', 'Api\AdminController@getPendingCourses');
$router->put('/api/admin/courses/:id/approve', 'Api\AdminController@approveCourse');
$router->put('/api/admin/courses/:id/reject', 'Api\AdminController@rejectCourse');
$router->get('/api/admin/vip-payments', 'Api\AdminController@getVipPayments');
$router->get('/api/admin/audit-logs', 'Api\AdminController@getAuditLogs');
