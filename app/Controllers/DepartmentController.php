<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Department;

class DepartmentController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('departments.view');
        $deptModel = new Department();
        $departments = $deptModel->getWithUserCounts();

        $this->render('departments.index', [
            'pageTitle' => 'Departments',
            'departments' => $departments
        ]);
    }

    public function store(Request $request): void {
        $this->requirePermission('departments.manage');
        $name = trim($request->input('name', ''));
        $code = strtoupper(trim($request->input('code', '')));
        $desc = trim($request->input('description', ''));

        if (empty($name) || empty($code)) {
            Response::error('Department name and code are required.');
        }

        $deptModel = new Department();
        if ($deptModel->findBy('code', $code)) {
            Response::error('Department code already exists.');
        }

        $id = $deptModel->create([
            'name' => $name,
            'code' => $code,
            'description' => $desc,
            'status' => 'active'
        ]);

        $this->logActivity('create', 'departments', (int)$id, $code, "Created department {$name}");
        Response::success('Department created successfully!', ['id' => $id]);
    }

    public function update(Request $request, array $params): void {
        $this->requirePermission('departments.manage');
        $id = (int)($params['id'] ?? 0);
        $deptModel = new Department();
        $dept = $deptModel->find($id);

        if (!$dept) {
            Response::error('Department not found.');
        }

        $deptModel->update($id, [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status', 'active')
        ]);

        $this->logActivity('update', 'departments', $id, $dept['code'], "Updated department {$dept['name']}");
        Response::success('Department updated successfully.');
    }

    public function delete(Request $request, array $params): void {
        $this->requirePermission('departments.manage');
        $id = (int)($params['id'] ?? 0);
        $deptModel = new Department();
        $dept = $deptModel->find($id);

        if (!$dept) {
            Response::error('Department not found.');
        }

        $deptModel->delete($id);
        $this->logActivity('delete', 'departments', $id, $dept['code'], "Deleted department {$dept['name']}");
        Response::success('Department deleted successfully.');
    }
}
