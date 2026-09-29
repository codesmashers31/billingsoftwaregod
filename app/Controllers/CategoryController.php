<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Category;
use App\Core\Session;
use App\Core\Database;

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

    public function template(Request $request): void {
        $this->requirePermission('categories.manage');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=categories_import_template.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Name', 'Code', 'Description', 'Status (Active/Inactive)']);
        fputcsv($output, ['God Statues', 'CAT-GOD-01', 'Various god statues', 'Active']);
        fclose($output);
        exit;
    }

    public function import(Request $request): void {
        $this->requirePermission('categories.manage');
        
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Please select a valid CSV file.');
            header('Location: ' . url('categories'));
            exit;
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        if (!$handle) {
            Session::flash('error', 'Failed to read the file.');
            header('Location: ' . url('categories'));
            exit;
        }

        $header = fgetcsv($handle);
        
        $db = Database::getInstance();
        $db->beginTransaction();
        
        $categoryModel = new Category();

        try {
            $count = 0;
            $rowNum = 1;
            $seenCodes = [];
            $seenNames = [];
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                $rowNum++;
                if (empty(array_filter($data))) continue;
                
                $name = trim($data[0] ?? '');
                $code = trim($data[1] ?? '');
                $desc = trim($data[2] ?? '');
                $status = strtolower(trim($data[3] ?? '')) === 'inactive' ? 'inactive' : 'active';
                
                if (empty($name) || empty($code)) {
                    throw new \Exception("Row {$rowNum}: Name and Code are required.");
                }
                
                if (in_array($code, $seenCodes)) {
                    throw new \Exception("Row {$rowNum}: Duplicate Code '{$code}' found within the CSV file.");
                }
                if (in_array(strtolower($name), $seenNames)) {
                    throw new \Exception("Row {$rowNum}: Duplicate Name '{$name}' found within the CSV file.");
                }
                $seenCodes[] = $code;
                $seenNames[] = strtolower($name);
                
                // Check against DB
                $existingCode = $categoryModel->where('code = ?', [$code]);
                if (!empty($existingCode)) {
                    throw new \Exception("Row {$rowNum}: Category with Code '{$code}' already exists.");
                }
                
                $existingName = $categoryModel->where('name = ?', [$name]);
                if (!empty($existingName)) {
                    throw new \Exception("Row {$rowNum}: Category with Name '{$name}' already exists.");
                }
                
                $categoryModel->create([
                    'name' => $name,
                    'code' => $code,
                    'description' => $desc,
                    'status' => $status
                ]);
                $count++;
            }
            
            fclose($handle);
            $db->commit();
            
            Session::flash('success', "Successfully imported $count new categories.");
        } catch (\Exception $e) {
            $db->rollback();
            Session::flash('error', 'Import failed: ' . $e->getMessage());
        }
        
        header('Location: ' . url('categories'));
        exit;
    }
}
