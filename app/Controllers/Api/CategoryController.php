<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\CategoryRepository;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;
use Exception;

/**
 * CategoryController
 * API endpoints cho danh mục khóa học.
 */
class CategoryController
{
    private CategoryRepository $categoryRepo;

    public function __construct()
    {
        $this->categoryRepo = new CategoryRepository();
    }

    /**
     * Danh sách categories
     * GET /api/categories
     */
    public function index(Request $request, Response $response)
    {
        try {
            $format = $request->input('format', 'flat'); // flat hoặc tree
            
            if ($format === 'tree') {
                $categories = $this->categoryRepo->findAllAsTree();
            } else {
                $categories = $this->categoryRepo->findAll();
            }
            
            $response->success("Danh mục khóa học", $categories);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }

    /**
     * Tạo category mới (Admin only)
     * POST /api/categories
     */
    public function store(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);

            $data = $request->all();
            if (empty($data['name'])) {
                throw new Exception("Tên danh mục không được để trống.");
            }

            $category = $this->categoryRepo->create($data);
            if ($category) {
                $response->success("Tạo danh mục thành công", $category, 201);
            } else {
                $response->error("Tạo danh mục thất bại", 400);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Cập nhật category (Admin only)
     * PUT /api/categories/:id
     */
    public function update(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);

            $data = $request->all();
            $this->categoryRepo->update((int)$id, $data);
            $response->success("Cập nhật danh mục thành công");
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Xóa category (Admin only)
     * DELETE /api/categories/:id
     */
    public function delete(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin']);

            $this->categoryRepo->delete((int)$id);
            $response->success("Đã xóa danh mục");
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
