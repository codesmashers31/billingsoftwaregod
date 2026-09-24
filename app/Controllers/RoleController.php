<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('roles.view');
        $roleModel = new Role();
        $permModel = new Permission();

        $roles = $roleModel->all('id ASC');
        $allPermissions = $permModel->getAllGroupedByModule();

        $this->render('roles.index', [
            'pageTitle' => 'Roles & Permissions Matrix',
            'roles' => $roles,
            'groupedPermissions' => $allPermissions,
        ]);
    }

    public function getPermissions(Request $request, array $params): void {
        $this->requirePermission('roles.view');
        $roleId = (int)($params['id'] ?? 0);
        $roleModel = new Role();
        $perms = $roleModel->getPermissions($roleId);
        Response::json([
            'success' => true,
            'permission_ids' => array_column($perms, 'id')
        ]);
    }

    public function updatePermissions(Request $request, array $params): void {
        $this->requirePermission('roles.manage');
        $roleId = (int)($params['id'] ?? 0);
        $roleModel = new Role();
        $role = $roleModel->find($roleId);

        if (!$role) {
            Response::error('Role not found.');
        }

        $permissionIds = (array)$request->input('permissions', []);
        $roleModel->syncPermissions($roleId, $permissionIds);

        $this->logActivity('update', 'roles', $roleId, $role['slug'], "Updated permissions for role {$role['name']}");
        Response::success('Role permissions updated successfully!');
    }

    public function store(Request $request): void {
        $this->requirePermission('roles.manage');
        $name = trim($request->input('name', ''));
        $desc = trim($request->input('description', ''));

        if (empty($name)) {
            Response::error('Role name is required.');
        }

        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
        $roleModel = new Role();

        if ($roleModel->findBy('slug', $slug)) {
            Response::error('A role with this name already exists.');
        }

        $id = $roleModel->create([
            'name' => $name,
            'slug' => $slug,
            'description' => $desc,
            'is_system' => 0,
            'status' => 'active'
        ]);

        $this->logActivity('create', 'roles', (int)$id, $slug, "Created new role {$name}");
        Response::success('Role created successfully!', ['id' => $id]);
    }
}
