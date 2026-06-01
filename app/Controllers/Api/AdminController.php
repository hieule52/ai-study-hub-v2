<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\UserRepository;
use App\Repositories\CourseRepository;
use App\Repositories\EnrollmentRepository;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;
use Exception;
use PDO;

use App\Repositories\NotificationRepository;

class AdminController
{
    private UserRepository $userRepo;
    private CourseRepository $courseRepo;
    private EnrollmentRepository $enrollRepo;
    private NotificationRepository $notifRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
        $this->courseRepo = new CourseRepository();
        $this->enrollRepo = new EnrollmentRepository();
        $this->notifRepo = new NotificationRepository();
    }

    public function getStats(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);

            $db = (new \App\Core\Database)::connect();

            // Sum prices from enrollments for total revenue
            $totalRevenue = (float)$db->query("
                SELECT SUM(c.price) 
                FROM enrollments e 
                JOIN courses c ON e.course_id = c.id
                WHERE c.deleted_at IS NULL
            ")->fetchColumn();

            $activeCoursesCount = (int)$db->query("SELECT COUNT(*) FROM courses WHERE status = 'approved' AND deleted_at IS NULL")->fetchColumn();

            $stats = [
                'total_revenue'    => $totalRevenue,
                'total_vip_users'  => $activeCoursesCount,
                'total_users'      => $this->userRepo->countUsers(),
                'pending_courses'  => $this->courseRepo->countPendingCourses()
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

            $page    = max(1, (int)($request->query('page') ?? 1));
            $limit   = min(100, max(10, (int)($request->query('limit') ?? 20)));
            $search  = trim($request->query('search') ?? '');
            $role    = $request->query('role') ?? '';
            $offset  = ($page - 1) * $limit;

            $users = $this->userRepo->getAllUsersPaginated($limit, $offset, $search, $role);
            $total = $this->userRepo->countUsersFiltered($search, $role);

            $response->success("Danh sách người dùng", [
                'items'      => $users,
                'pagination' => [
                    'page'        => $page,
                    'limit'       => $limit,
                    'total'       => $total,
                    'total_pages' => (int)ceil($total / $limit),
                ]
            ]);
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

    public function getAllCourses(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            $courses = $this->courseRepo->getAllAdminCourses();
            $response->success("Danh sách toàn bộ khóa học", $courses);
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
            
            $course = $this->courseRepo->findById((int)$id);
            if (!$course) throw new Exception("Không tìm thấy khóa học.");

            $success = $this->courseRepo->updateStatus((int)$id, 'approved');
            if ($success) {
                // Mark related moderation notifications as read
                $db = \App\Core\Database::connect();
                $stmt = $db->prepare("UPDATE admin_notifications SET is_read = 1 WHERE course_id = ?");
                $stmt->execute([(int)$id]);

                // Create Notification for Teacher
                $this->notifRepo->create(
                    (int)$course->teacher_id,
                    'course_approved',
                    '🎉 Khóa học đã được duyệt!',
                    "Chúc mừng! Khóa học '{$course->title}' của bạn đã được phê duyệt và hiển thị công khai."
                );
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
            
            $course = $this->courseRepo->findById((int)$id);
            if (!$course) throw new Exception("Không tìm thấy khóa học.");

            // Chuyển lại về draft để người upload có thể sửa lại
            $success = $this->courseRepo->updateStatus((int)$id, 'draft');
            if ($success) {
                // Mark related moderation notifications as read
                $db = \App\Core\Database::connect();
                $stmt = $db->prepare("UPDATE admin_notifications SET is_read = 1 WHERE course_id = ?");
                $stmt->execute([(int)$id]);

                // Create Notification for Teacher
                $this->notifRepo->create(
                    (int)$course->teacher_id,
                    'warning',
                    '⚠️ Khóa học cần chỉnh sửa',
                    "Khóa học '{$course->title}' đã bị từ chối phê duyệt. Vui lòng kiểm tra lại nội dung và gửi duyệt lại."
                );
                $response->success("Đã từ chối và chuyển khóa học về bản nháp.");
            } else {
                $response->error("Không thể cập nhật trạng thái khóa học.", 500);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function hideCourse(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);

            $course = $this->courseRepo->findById((int)$id);
            if (!$course) throw new Exception("Không tìm thấy khóa học.");

            $success = $this->courseRepo->updateStatus((int)$id, 'hidden');
            if ($success) {
                $this->notifRepo->create(
                    (int)$course->teacher_id,
                    'warning',
                    '⚠️ Khóa học đã bị ẩn bởi Admin',
                    "Khóa học '{$course->title}' của bạn đã bị ẩn bởi quản trị viên. Vui lòng liên hệ để biết thêm chi tiết."
                );
                $response->success("Đã ẩn khóa học.");
            } else {
                $response->error("Không thể ẩn khóa học.", 500);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function showCourse(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);

            $course = $this->courseRepo->findById((int)$id);
            if (!$course) throw new Exception("Không tìm thấy khóa học.");

            $success = $this->courseRepo->updateStatus((int)$id, 'approved');
            if ($success) {
                $this->notifRepo->create(
                    (int)$course->teacher_id,
                    'course_approved',
                    '🎉 Khóa học đã hiển thị lại',
                    "Khóa học '{$course->title}' của bạn đã được hiển thị lại công khai trên hệ thống."
                );
                $response->success("Đã hiển thị lại khóa học.");
            } else {
                $response->error("Không thể hiển thị khóa học.", 500);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function deleteCourse(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);

            $course = $this->courseRepo->findById((int)$id);
            if (!$course) throw new Exception("Không tìm thấy khóa học.");

            $success = $this->courseRepo->delete((int)$id);
            if ($success) {
                $this->notifRepo->create(
                    (int)$course->teacher_id,
                    'warning',
                    '❌ Khóa học đã bị xóa bởi Admin',
                    "Khóa học '{$course->title}' của bạn đã bị xóa khỏi hệ thống bởi quản trị viên."
                );
                $response->success("Đã xóa khóa học thành công (Soft Delete).");
            } else {
                $response->error("Không thể xóa khóa học.", 500);
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

            $db = (new \App\Core\Database)::connect();

            // Build 6-month enrollment chart
            $labels = [];
            $data   = [];
            for ($i = 5; $i >= 0; $i--) {
                $time  = strtotime("-$i months");
                $month = date('Y-m', $time);
                $label = 'Tháng ' . date('n', $time);
                $labels[] = $label;

                $stmt = $db->prepare("
                    SELECT COUNT(e.id) 
                    FROM enrollments e
                    JOIN courses c ON e.course_id = c.id
                    WHERE DATE_FORMAT(e.enrolled_at, '%Y-%m') = :ym AND c.deleted_at IS NULL
                ");
                $stmt->execute(['ym' => $month]);
                $data[] = (int)$stmt->fetchColumn();
            }

            $chartData = [
                'labels'   => $labels,
                'datasets' => [[
                    'label'           => 'Lượt ghi danh',
                    'data'            => $data,
                    'borderColor'     => '#818cf8',
                    'backgroundColor' => 'rgba(129,140,248,0.15)',
                    'borderWidth'     => 2,
                    'fill'            => true,
                    'tension'         => 0.4,
                    'pointBackgroundColor' => '#818cf8',
                    'pointRadius'     => 4
                ]]
            ];

            $response->success("Biểu đồ ghi danh", $chartData);
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

            // Whitelist: only allow safe fields to be updated via this endpoint
            $allowed = ['username', 'email', 'is_vip', 'status', 'role', 'password'];
            $safeData = array_intersect_key($body, array_flip($allowed));

            if (!empty($safeData['password'])) {
                $safeData['password'] = password_hash($safeData['password'], PASSWORD_DEFAULT);
            }

            if (empty($safeData)) {
                $response->error("Không có trường hợp lệ để cập nhật.", 400);
                return;
            }

            $success = $this->userRepo->update((int)$id, $safeData);
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

    public function getEnrollments(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            
            $enrollments = $this->enrollRepo->getAllEnrollments();
            
            $db = (new \App\Core\Database)::connect();
            $totalRevenue = (float)$db->query("
                SELECT SUM(c.price) 
                FROM enrollments e 
                JOIN courses c ON e.course_id = c.id
                WHERE c.deleted_at IS NULL
            ")->fetchColumn();

            $response->success("Danh sách ghi danh", [
                'items' => $enrollments,
                'total_revenue' => $totalRevenue
            ]);
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

    public function getNotifications(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            
            $db = \App\Core\Database::connect();
            $stmt = $db->query("
                SELECT an.*, c.title as course_title, u.username as teacher_name
                FROM admin_notifications an
                LEFT JOIN courses c ON an.course_id = c.id
                LEFT JOIN users u ON c.teacher_id = u.id
                ORDER BY an.created_at DESC
            ");
            $notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($notifs as &$n) {
                if ($n['data']) {
                    $n['data'] = json_decode($n['data'], true);
                }
            }
            $response->success("Danh sách thông báo kiểm duyệt", $notifs);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function markNotificationsAllRead(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            
            $db = \App\Core\Database::connect();
            $db->query("UPDATE admin_notifications SET is_read = 1 WHERE is_read = 0");
            $response->success("Đã đánh dấu tất cả thông báo là đã đọc.");
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function markNotificationRead(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            
            $db = \App\Core\Database::connect();
            $stmt = $db->prepare("UPDATE admin_notifications SET is_read = 1 WHERE id = ?");
            $stmt->execute([(int)$id]);
            $response->success("Đã đánh dấu thông báo là đã đọc.");
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function getCourseChanges(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);
            
            $db = \App\Core\Database::connect();
            $stmt = $db->prepare("
                SELECT ccl.*, u.username as performer_name
                FROM course_change_logs ccl
                LEFT JOIN users u ON ccl.performed_by = u.id
                WHERE ccl.course_id = ?
                ORDER BY ccl.created_at DESC
            ");
            $stmt->execute([(int)$id]);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($logs as &$l) {
                if ($l['changed_fields']) $l['changed_fields'] = json_decode($l['changed_fields'], true);
                if ($l['old_snapshot']) $l['old_snapshot'] = json_decode($l['old_snapshot'], true);
                if ($l['new_snapshot']) $l['new_snapshot'] = json_decode($l['new_snapshot'], true);
            }
            $response->success("Lịch sử thay đổi khóa học", $logs);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
