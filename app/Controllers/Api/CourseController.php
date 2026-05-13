<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\CourseService;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;
use App\Repositories\CourseRepository;
use Exception;

class CourseController
{
    private CourseService $courseService;
    private CourseRepository $courseRepo;

    public function __construct()
    {
        $this->courseService = new CourseService();
        $this->courseRepo    = new CourseRepository();
    }

    /**
     * GET /api/courses?page=1&limit=12&category=&level=
     */
    public function index(Request $request, Response $response)
    {
        try {
            $page  = max(1, (int)($request->query('page')  ?? 1));
            $limit = min(50, max(6, (int)($request->query('limit') ?? 12)));
            $offset = ($page - 1) * $limit;

            $courses = $this->courseService->getAllCourses($page, $limit);
            $total   = $this->courseRepo->countApproved();

            $response->success("Thành công", [
                'items'      => $courses,
                'pagination' => [
                    'page'        => $page,
                    'limit'       => $limit,
                    'total'       => $total,
                    'total_pages' => (int)ceil($total / $limit),
                ]
            ]);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/courses/search?q=keyword&category_id=&level=&page=1
     */
    public function search(Request $request, Response $response)
    {
        try {
            $keyword    = trim($request->query('q') ?? '');
            $categoryId = (int)($request->query('category_id') ?? 0);
            $level      = $request->query('level') ?? '';
            $page       = max(1, (int)($request->query('page') ?? 1));
            $limit      = 12;

            if ($keyword !== '') {
                $courses = $this->courseRepo->search($keyword, $limit);
            } elseif ($categoryId > 0) {
                $courses = $this->courseRepo->findByCategory($categoryId, $limit);
            } else {
                $courses = $this->courseRepo->findAll($limit, ($page - 1) * $limit);
            }

            $response->success("Kết quả tìm kiếm", $courses);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }

    public function show(Request $request, Response $response, string $id)
    {
        try {
            $course = $this->courseService->getCourseDetail((int)$id);
            $response->success("Chi tiết khóa học", (array)$course);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 404);
        }
    }

    public function store(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin', 'teacher']);

            $data = $request->all();
            $teacherId = $request->user->sub; // Lấy từ token subject
            
            $course = $this->courseService->createCourse($data, $teacherId);
            $response->success("Tạo khóa học thành công", (array)$course, 201);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function getReviews(Request $request, Response $response, string $id)
    {
        try {
            $stmt = \App\Core\Database::connect()->prepare("
                SELECT cr.*, u.username 
                FROM course_reviews cr 
                JOIN users u ON cr.user_id = u.id 
                WHERE cr.course_id = :course_id 
                ORDER BY cr.created_at DESC
            ");
            $stmt->execute(['course_id' => (int)$id]);
            $reviews = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Calculate aggregate
            $stmtAvg = \App\Core\Database::connect()->prepare("SELECT AVG(rating) as avg_rating, COUNT(id) as total_reviews FROM course_reviews WHERE course_id = :course_id");
            $stmtAvg->execute(['course_id' => (int)$id]);
            $stats = $stmtAvg->fetch(\PDO::FETCH_ASSOC);

            $response->success("Đánh giá khóa học", [
                'reviews' => $reviews,
                'stats' => [
                    'avg_rating' => $stats['avg_rating'] ? number_format((float)$stats['avg_rating'], 1) : 0,
                    'total_reviews' => (int)$stats['total_reviews']
                ]
            ]);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }
}

