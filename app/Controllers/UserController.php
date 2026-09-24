<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;

class UserController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('users.view');
        $userModel = new User();
        $roleModel = new Role();
        $deptModel = new Department();

        $page = (int)$request->input('page', 1);
        $search = (string)$request->input('search', '');
        $roleFilter = (string)$request->input('role', '');
        $statusFilter = (string)$request->input('status', '');

        $result = $userModel->getPaginatedList($page, 15, $search, $roleFilter, $statusFilter);
        $roles = $roleModel->all('name ASC');
        $departments = $deptModel->all('name ASC');

        if ($request->isAjax()) {
            Response::json($result);
        }

        $this->render('users.index', [
            'pageTitle' => 'User Management',
            'users' => $result['data'],
            'pagination' => $result,
            'roles' => $roles,
            'departments' => $departments,
            'search' => $search,
            'roleFilter' => $roleFilter,
            'statusFilter' => $statusFilter
        ]);
    }

    public function store(Request $request): void {
        $this->requirePermission('users.create');

        $errors = $this->validate($request->all(), [
            'name' => 'required|min:2',
            'username' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role_id' => 'required'
        ]);

        $userModel = new User();
        if ($userModel->findBy('username', $request->input('username'))) {
            $errors['username'][] = 'Username is already taken.';
        }
        if ($userModel->findBy('email', $request->input('email'))) {
            $errors['email'][] = 'Email address is already in use.';
        }

        if (!empty($errors)) {
            if ($request->isAjax()) Response::error('Validation failed', $errors);
            Session::flash('error', 'Please fix form errors.');
            Response::redirect('/users');
        }

        $id = $userModel->create([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
            'password' => password_hash($request->input('password'), PASSWORD_BCRYPT),
            'role_id' => (int)$request->input('role_id'),
            'department_id' => $request->input('department_id') ? (int)$request->input('department_id') : null,
            'status' => $request->input('status', 'active'),
        ]);

        $this->logActivity('create', 'users', (int)$id, $request->input('username'), "Created user {$request->input('name')}");

        if ($request->isAjax()) {
            Response::success('User created successfully!', ['id' => $id]);
        }

        Session::flash('success', 'User created successfully.');
        Response::redirect('/users');
    }

    public function update(Request $request, array $params): void {
        $this->requirePermission('users.edit');
        $id = (int)($params['id'] ?? 0);
        $userModel = new User();
        $user = $userModel->find($id);

        if (!$user) {
            if ($request->isAjax()) Response::error('User not found.');
            Session::flash('error', 'User not found.');
            Response::redirect('/users');
        }

        $updateData = [
            'name' => $request->input('name'),
            'mobile' => $request->input('mobile'),
            'role_id' => (int)$request->input('role_id'),
            'department_id' => $request->input('department_id') ? (int)$request->input('department_id') : null,
            'status' => $request->input('status', 'active')
        ];

        // Optional password update
        $newPass = $request->input('password');
        if (!empty($newPass)) {
            if (strlen($newPass) < 6) {
                if ($request->isAjax()) Response::error('Password must be at least 6 characters.');
                Session::flash('error', 'Password must be at least 6 characters.');
                Response::redirect('/users');
            }
            $updateData['password'] = password_hash($newPass, PASSWORD_BCRYPT);
        }

        $userModel->update($id, $updateData);
        $this->logActivity('update', 'users', $id, $user['username'], "Updated user profile {$user['name']}");

        if ($request->isAjax()) {
            Response::success('User updated successfully!');
        }

        Session::flash('success', 'User updated successfully.');
        Response::redirect('/users');
    }

    public function toggleStatus(Request $request, array $params): void {
        $this->requirePermission('users.edit');
        $id = (int)($params['id'] ?? 0);
        $userModel = new User();
        $user = $userModel->find($id);

        if (!$user) {
            Response::error('User not found.');
        }

        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        $userModel->update($id, ['status' => $newStatus]);
        $this->logActivity('update', 'users', $id, $user['username'], "Toggled user status to {$newStatus}");

        Response::success("User status changed to {$newStatus}.", ['status' => $newStatus]);
    }

    public function delete(Request $request, array $params): void {
        $this->requirePermission('users.delete');
        $id = (int)($params['id'] ?? 0);
        $userModel = new User();
        $user = $userModel->find($id);

        if (!$user) {
            Response::error('User not found.');
        }

        if ($id === 1 || $user['username'] === 'superadmin') {
            Response::error('Super admin account cannot be deleted.');
        }

        $userModel->delete($id);
        $this->logActivity('delete', 'users', $id, $user['username'], "Deleted user {$user['name']}");

        Response::success('User deleted successfully.');
    }
}
