<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Supplier;

class SupplierController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('suppliers.view');
        $suppModel = new Supplier();

        $page = (int)$request->input('page', 1);
        $search = (string)$request->input('search', '');

        $conditions = ["1=1"];
        $params = [];
        if (!empty($search)) {
            $conditions[] = "(name LIKE ? OR company_name LIKE ? OR mobile LIKE ? OR supplier_code LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term]);
        }

        $result = $suppModel->paginate($page, 15, implode(' AND ', $conditions), $params, 'id DESC');

        if ($request->isAjax() && $request->input('ajax_table')) {
            Response::json($result);
        }

        $this->render('suppliers.index', [
            'pageTitle' => 'Artisans & Suppliers Directory',
            'suppliers' => $result['data'],
            'pagination' => $result,
            'search' => $search
        ]);
    }

    public function store(Request $request): void {
        $this->requirePermission('suppliers.manage');
        $name = trim($request->input('name', ''));
        $mobile = trim($request->input('mobile', ''));

        if (empty($name) || empty($mobile)) {
            Response::error('Supplier name and mobile number are required.');
        }

        $suppModel = new Supplier();
        $code = 'SUP-' . str_pad((string)($suppModel->count() + 1), 4, '0', STR_PAD_LEFT);

        $id = $suppModel->create([
            'supplier_code' => $code,
            'name' => $name,
            'company_name' => $request->input('company_name'),
            'mobile' => $mobile,
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'gst_number' => $request->input('gst_number'),
            'bank_name' => $request->input('bank_name'),
            'bank_account_no' => $request->input('bank_account_no'),
            'bank_ifsc' => $request->input('bank_ifsc'),
            'status' => 'active'
        ]);

        $this->logActivity('create', 'suppliers', (int)$id, $code, "Added supplier/artisan {$name}");
        Response::success('Supplier registered successfully!', ['id' => $id]);
    }

    public function update(Request $request, array $params): void {
        $this->requirePermission('suppliers.manage');
        $id = (int)($params['id'] ?? 0);
        $suppModel = new Supplier();
        $supp = $suppModel->find($id);

        if (!$supp) {
            Response::error('Supplier not found.');
        }

        $suppModel->update($id, [
            'name' => $request->input('name'),
            'company_name' => $request->input('company_name'),
            'mobile' => $request->input('mobile'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'gst_number' => $request->input('gst_number'),
            'bank_name' => $request->input('bank_name'),
            'bank_account_no' => $request->input('bank_account_no'),
            'bank_ifsc' => $request->input('bank_ifsc'),
            'status' => $request->input('status', 'active')
        ]);

        $this->logActivity('update', 'suppliers', $id, $supp['supplier_code'], "Updated supplier {$supp['name']}");
        Response::success('Supplier updated successfully.');
    }
}
