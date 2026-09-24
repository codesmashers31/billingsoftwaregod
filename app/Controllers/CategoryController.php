<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Category;

class CategoryController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('categories.view');
        $categoryModel = new Category();
        $categories = $categoryModel->getWithCounts();

        if ($request->isAjax()) {
            Response::json(['data' => $categories]);
        }

        $this->render('categories.index', [
            'pageTitle' => 'Product Categories',
            'categories' => $categories
        ]);
    }

    public function store(Request $request): void {
        $this->requirePermission('categories.manage');
        $name = trim($request->input('name', ''));
        $code = strtoupper(trim($request->input('code', '')));
        $desc = trim($request->input('description', ''));

        if (empty($name) || empty($code)) {
            Response::error('Category name and code are required.');
        }

        $categoryModel = new Category();
        if ($categoryModel->findBy('code', $code)) {
            Response::error('Category code already exists.');
        }

        $id = $categoryModel->create([
            'name' => $name,
            'code' => $code,
            'description' => $desc,
            'status' => 'active'
        ]);

        $this->logActivity('create', 'catalog', (int)$id, $code, "Created category {$name}");
        Response::success('Category created successfully!', ['id' => $id]);
    }

    public function update(Request $request, array $params): void {
        $this->requirePermission('categories.manage');
        $id = (int)($params['id'] ?? 0);
        $categoryModel = new Category();
        $cat = $categoryModel->find($id);

        if (!$cat) {
            Response::error('Category not found.');
        }

        $categoryModel->update($id, [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status', 'active')
        ]);

        $this->logActivity('update', 'catalog', $id, $cat['code'], "Updated category {$cat['name']}");
        Response::success('Category updated successfully.');
    }

    public function delete(Request $request, array $params): void {
        $this->requirePermission('categories.manage');
        $id = (int)($params['id'] ?? 0);
        $categoryModel = new Category();
        $cat = $categoryModel->find($id);

        if (!$cat) {
            Response::error('Category not found.');
        }

        $categoryModel->delete($id);
        $this->logActivity('delete', 'catalog', $id, $cat['code'], "Deleted category {$cat['name']}");
        Response::success('Category deleted successfully.');
    }
}
