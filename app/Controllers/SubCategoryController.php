<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\SubCategory;
use App\Models\Category;
use App\Core\Session;
use App\Core\Database;

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

    public function template(Request $request): void {
        $this->requirePermission('categories.manage');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=subcategories_import_template.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Category ID', 'Subcategory Name', 'Code', 'Description', 'Status (Active/Inactive)']);
        fputcsv($output, ['1', 'Ganesha Forms', 'SUB-GAN-01', 'Various Ganesha postures', 'Active']);
        fclose($output);
        exit;
    }

    public function import(Request $request): void {
        $this->requirePermission('categories.manage');
        
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Please select a valid CSV file.');
            header('Location: ' . url('subcategories'));
            exit;
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        if (!$handle) {
            Session::flash('error', 'Failed to read the file.');
            header('Location: ' . url('subcategories'));
            exit;
        }

        $header = fgetcsv($handle);
        
        $db = Database::getInstance();
        $db->beginTransaction();
        
        $subModel = new SubCategory();

        try {
            $count = 0;
            $rowNum = 1;
            $seenCodes = [];
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                $rowNum++;
                if (empty(array_filter($data))) continue;
                
                $categoryId = trim($data[0] ?? '');
                $name = trim($data[1] ?? '');
                $code = trim($data[2] ?? '');
                $desc = trim($data[3] ?? '');
                $status = strtolower(trim($data[4] ?? '')) === 'inactive' ? 'inactive' : 'active';
                
                if (empty($categoryId) || !is_numeric($categoryId)) {
                    throw new \Exception("Row {$rowNum}: Category ID is required and must be a number.");
                }
                if (empty($name) || empty($code)) {
                    throw new \Exception("Row {$rowNum}: Subcategory Name and Code are required.");
                }
                
                if (in_array($code, $seenCodes)) {
                    throw new \Exception("Row {$rowNum}: Duplicate Code '{$code}' found within the CSV file.");
                }
                $seenCodes[] = $code;
                
                // Check against DB for Duplicate Code
                $existingCode = $subModel->where('code = ?', [$code]);
                if (!empty($existingCode)) {
                    throw new \Exception("Row {$rowNum}: Subcategory with Code '{$code}' already exists.");
                }
                
                // Check against DB for Duplicate Name in same Category
                $existingName = $subModel->where('name = ? AND category_id = ?', [$name, $categoryId]);
                if (!empty($existingName)) {
                    throw new \Exception("Row {$rowNum}: Subcategory Name '{$name}' already exists under Category ID '{$categoryId}'.");
                }
                
                $subModel->create([
                    'category_id' => $categoryId,
                    'name' => $name,
                    'code' => $code,
                    'description' => $desc,
                    'status' => $status
                ]);
                $count++;
            }
            
            fclose($handle);
            $db->commit();
            
            Session::flash('success', "Successfully imported $count new subcategories.");
        } catch (\Exception $e) {
            $db->rollback();
            Session::flash('error', 'Import failed: ' . $e->getMessage());
        }
        
        header('Location: ' . url('subcategories'));
        exit;
    }
}
