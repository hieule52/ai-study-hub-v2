<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\LearningPathService;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;
use Exception;

/**
 * LearningPathController
 * API endpoints cho hệ thống lộ trình học (Learning Path / Roadmap).
 */
class LearningPathController
{
    private LearningPathService $pathService;

    public function __construct()
    {
        $this->pathService = new LearningPathService();
    }

    /**
     * Danh sách lộ trình
     * GET /api/learning-paths
     */
    public function index(Request $request, Response $response)
    {
        try {
            $paths = $this->pathService->getAllPublished();
            $response->success("Danh sách lộ trình học", $paths);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }

    /**
     * Chi tiết lộ trình + danh sách khóa học
     * GET /api/learning-paths/:id
     */
    public function show(Request $request, Response $response, string $id)
    {
        try {
            // Nếu có JWT → kèm tiến độ
            $authHeader = $request->getHeader('Authorization');
            if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                try {
                    AuthMiddleware::handle($request, $response);
                    $path = $this->pathService->getPathDetailWithProgress((int)$id, $request->user->sub);
                } catch (Exception $e) {
                    // Token hết hạn → trả về không có progress
                    $path = $this->pathService->getPathDetail((int)$id);
                }
            } else {
                $path = $this->pathService->getPathDetail((int)$id);
            }

            $response->success("Chi tiết lộ trình", $path);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 404);
        }
    }

    /**
     * Tạo lộ trình mới (Teacher/Admin)
     * POST /api/learning-paths
     */
    public function store(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $data = $request->all();
            $creatorId = $request->user->sub;

            $path = $this->pathService->createPath($data, $creatorId);
            $response->success("Tạo lộ trình thành công", $path, 201);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Cập nhật lộ trình
     * PUT /api/learning-paths/:id
     */
    public function update(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $data = $request->all();
            $this->pathService->updatePath((int)$id, $data);
            $response->success("Cập nhật lộ trình thành công");
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Thêm khóa học vào lộ trình
     * POST /api/learning-paths/:id/courses
     * Body: { course_id, order_index?, is_required? }
     */
    public function addCourse(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $data = $request->all();
            if (empty($data['course_id'])) {
                throw new Exception("Thiếu course_id.");
            }

            $order = (int)($data['order_index'] ?? 0);
            $required = isset($data['is_required']) ? (bool)$data['is_required'] : true;

            $this->pathService->addCourseToPath((int)$id, (int)$data['course_id'], $order, $required);
            $response->success("Đã thêm khóa học vào lộ trình", null, 201);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Xóa khóa học khỏi lộ trình
     * DELETE /api/learning-paths/:pathId/courses/:courseId
     */
    public function removeCourse(Request $request, Response $response, string $pathId, string $courseId)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $this->pathService->removeCourseFromPath((int)$pathId, (int)$courseId);
            $response->success("Đã xóa khóa học khỏi lộ trình");
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Student đăng ký lộ trình
     * POST /api/learning-paths/:id/enroll
     */
    public function enroll(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);

            $userId = $request->user->sub;
            $result = $this->pathService->enrollInPath($userId, (int)$id);
            $response->success($result['message'], $result);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Tiến độ lộ trình của student
     * GET /api/learning-paths/:id/progress
     */
    public function progress(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);

            $userId = $request->user->sub;
            $progress = $this->pathService->calculateProgress($userId, (int)$id);
            $response->success("Tiến độ lộ trình", $progress);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Danh sách lộ trình đã enrolled của user
     * GET /api/learning-paths/my
     */
    public function myPaths(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);

            $userId = $request->user->sub;
            $paths = $this->pathService->getUserPaths($userId);
            $response->success("Lộ trình của bạn", $paths);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
