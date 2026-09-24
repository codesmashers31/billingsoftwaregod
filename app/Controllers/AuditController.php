<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\AuditLog;

class AuditController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('audit.view');
        $auditModel = new AuditLog();
        $db = Database::getInstance();

        $page = (int)$request->input('page', 1);
        $filters = [
            'search' => (string)$request->input('search', ''),
            'module' => (string)$request->input('module', ''),
            'action' => (string)$request->input('action', ''),
            'user_id' => (string)$request->input('user_id', ''),
        ];

        $result = $auditModel->getPaginatedList($page, 20, $filters);
        $users = $db->query("SELECT id, name, username FROM `users` ORDER BY name ASC");

        if ($request->isAjax() && $request->input('ajax_table')) {
            Response::json($result);
        }

        $this->render('audit.index', [
            'pageTitle' => 'System Audit Logs & Security Trails',
            'logs' => $result['data'],
            'pagination' => $result,
            'users' => $users,
            'filters' => $filters
        ]);
    }
}
