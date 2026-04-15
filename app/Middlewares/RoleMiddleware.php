<?php

namespace App\Middlewares;

use App\Core\Request;
use App\Core\Response;

class RoleMiddleware
{
    /**
     * @param array $allowedRoles Danh sách các role được phép (vd: ['admin', 'teacher'])
     */
    public static function handle(Request $request, Response $response, array $allowedRoles)
    {
        // Chắc chắn AuthMiddleware đã được chạy trước để nhúng user vào request
        if (!isset($request->user) || !isset($request->user->role)) {
            $response->error('Unauthorized', 401);
        }

        $userRole = $request->user->role;

        if (!in_array($userRole, $allowedRoles)) {
            $response->error('Forbidden - Bạn không có quyền truy cập chức năng này', 403);
        }

        return true;
    }
}
