<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\UserRepository;
use App\Repositories\CourseRepository;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;
use Exception;

class AdminController
{
    private UserRepository $userRepo;
    private CourseRepository $courseRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
        $this->courseRepo = new CourseRepository();
    }

    public function getStats(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);

            $stats = [
                'total_revenue' => $this->courseRepo->getTotalRevenue(),
                'total_vip_users' => $this->userRepo->countVipUsers(),
                'total_users' => $this->userRepo->countUsers(),
                'pending_courses' => $this->courseRepo->countPendingCourses()
            ];

            $response->success("Admin Stats", $stats);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function getUsers(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            $users = $this->userRepo->getAllUsers();
            $response->success("Danh sách người dùng", $users);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function updateUserStatus(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            $body = $request->all();
            if (!isset($body['status']) || !in_array($body['status'], ['active', 'banned'])) {
                $response->error("Trạng thái không hợp lệ.", 400);
                return;
            }

            $success = $this->userRepo->updateStatus((int)$id, $body['status']);
            if ($success) {
                $response->success("Đã cập nhật trạng thái người dùng thành công.");
            } else {
                $response->error("Không thể cập nhật trạng thái.", 500);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function updateUserRole(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            $body = $request->all();
            if (!isset($body['role']) || !in_array($body['role'], ['student', 'teacher', 'admin'])) {
                $response->error("Quyền không hợp lệ.", 400);
                return;
            }

            $success = $this->userRepo->updateRole((int)$id, $body['role']);
            if ($success) {
                $response->success("Đã phân quyền thành công.");
            } else {
                $response->error("Không thể cập nhật quyền.", 500);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function getPendingCourses(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            $courses = $this->courseRepo->getPendingCourses();
            $response->success("Danh sách khóa học chờ duyệt", $courses);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function approveCourse(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            $success = $this->courseRepo->updateStatus((int)$id, 'approved');
            if ($success) {
                $response->success("Đã duyệt khóa học thành công!");
            } else {
                $response->error("Không thể duyệt khóa học.", 500);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function rejectCourse(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            // Chuyển lại về draft để người upload có thể sửa lại
            $success = $this->courseRepo->updateStatus((int)$id, 'draft');
            if ($success) {
                $response->success("Đã từ chối và chuyển khóa học về bản nháp.");
            } else {
                $response->error("Không thể cập nhật trạng thái khóa học.", 500);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function getChartData(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            
            $dbData = $this->courseRepo->getMonthlyRevenue(6);

            $chartData = [
                'labels' => $dbData['labels'],
                'datasets' => [
                    [
                        'label' => 'Doanh Thu (VNĐ) - Dữ liệu thực',
                        'data' => $dbData['data'],
                        'borderColor' => '#facc15',
                        'backgroundColor' => 'rgba(250, 204, 21, 0.2)',
                        'borderWidth' => 3,
                        'fill' => true,
                        'tension' => 0.4
                    ]
                ]
            ];
            $response->success("Biểu đồ doanh thu thực tế", $chartData);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function updateUser(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            $body = $request->all();
            
            $success = $this->userRepo->update((int)$id, $body);
            if ($success) {
                $response->success("Đã cập nhật thông tin người dùng thành công.");
            } else {
                $response->error("Không thể cập nhật thông tin.", 500);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function deleteUser(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            $success = $this->userRepo->delete((int)$id);
            if ($success) {
                $response->success("Đã xóa tài khoản thành công (Soft Delete).");
            } else {
                $response->error("Không thể xóa tài khoản.", 500);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function getVipPayments(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            $payments = $this->userRepo->getVipPayments();
            $response->success("Danh sách giao dịch VIP", $payments);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function getAuditLogs(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            $logs = $this->userRepo->getAuditLogs();
            $response->success("Danh sách Nhật ký hệ thống", $logs);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
