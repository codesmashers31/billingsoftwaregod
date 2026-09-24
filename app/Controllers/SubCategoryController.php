<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\SubCategory;
use App\Models\Category;

class SubCategoryController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('categories.view');
        $subModel = new SubCategory();
        $catModel = new Category();

        $subcategories = $subModel->getWithCategory();
        $categories = $catModel->where("status = 'active'", [], 'name ASC');

        if ($request->isAjax()) {
            Response::json(['data' => $subcategories]);
        }

        $this->render('subcategories.index', [
            'pageTitle' => 'Subcategories (Deities & Item Types)',
            'subcategories' => $subcategories,
            'categories' => $categories
        ]);
    }

    public function getByCategory(Request $request, array $params): void {
        $categoryId = (int)($params['id'] ?? 0);
        $subModel = new SubCategory();
        $subs = $subModel->getByCategory($categoryId);
        Response::json(['data' => $subs]);
    }

    public function store(Request $request): void {
        $this->requirePermission('categories.manage');
        $name = trim($request->input('name', ''));
        $code = strtoupper(trim($request->input('code', '')));
        $categoryId = (int)$request->input('category_id');
        $desc = trim($request->input('description', ''));

        if (empty($name) || empty($code) || empty($categoryId)) {
            Response::error('Category, subcategory name, and code are required.');
        }

        $subModel = new SubCategory();
        if ($subModel->findBy('code', $code)) {
            Response::error('Subcategory code already exists.');
        }

        $id = $subModel->create([
            'category_id' => $categoryId,
            'name' => $name,
            'code' => $code,
            'description' => $desc,
            'status' => 'active'
        ]);

        $this->logActivity('create', 'catalog', (int)$id, $code, "Created subcategory {$name}");
        Response::success('Subcategory created successfully!', ['id' => $id]);
    }

    public function update(Request $request, array $params): void {
        $this->requirePermission('categories.manage');
        $id = (int)($params['id'] ?? 0);
        $subModel = new SubCategory();
        $sub = $subModel->find($id);

        if (!$sub) {
            Response::error('Subcategory not found.');
        }

        $subModel->update($id, [
            'category_id' => (int)$request->input('category_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status', 'active')
        ]);

        $this->logActivity('update', 'catalog', $id, $sub['code'], "Updated subcategory {$sub['name']}");
        Response::success('Subcategory updated successfully.');
    }

    public function delete(Request $request, array $params): void {
        $this->requirePermission('categories.manage');
        $id = (int)($params['id'] ?? 0);
        $subModel = new SubCategory();
        $sub = $subModel->find($id);

        if (!$sub) {
            Response::error('Subcategory not found.');
        }

        $subModel->delete($id);
        $this->logActivity('delete', 'catalog', $id, $sub['code'], "Deleted subcategory {$sub['name']}");
        Response::success('Subcategory deleted successfully.');
    }
}
