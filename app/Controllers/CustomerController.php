<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Customer;

class CustomerController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('customers.view');
        $custModel = new Customer();

        $page = (int)$request->input('page', 1);
        $search = (string)$request->input('search', '');
        $typeFilter = (string)$request->input('type', '');

        $conditions = ["1=1"];
        $params = [];
        if (!empty($search)) {
            $conditions[] = "(name LIKE ? OR mobile LIKE ? OR customer_code LIKE ? OR email LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term]);
        }
        if (!empty($typeFilter)) {
            $conditions[] = "customer_type = ?";
            $params[] = $typeFilter;
        }

        $result = $custModel->paginate($page, 15, implode(' AND ', $conditions), $params, 'id DESC');

        if ($request->isAjax() && $request->input('ajax_table')) {
            Response::json($result);
        }

        $this->render('customers.index', [
            'pageTitle' => 'Customer Management & Ledgers',
            'customers' => $result['data'],
            'pagination' => $result,
            'search' => $search,
            'typeFilter' => $typeFilter
        ]);
    }

    public function search(Request $request): void {
        $term = (string)$request->input('q', '');
        $custModel = new Customer();
        $customers = $custModel->search($term, 15);
        Response::json(['data' => $customers]);
    }

    public function store(Request $request): void {
        $this->requirePermission('customers.create');
        $name = trim($request->input('name', ''));
        $mobile = trim($request->input('mobile', ''));

        if (empty($name) || empty($mobile)) {
            Response::error('Customer name and mobile number are required.');
        }

        $custModel = new Customer();
        $code = 'CUST-' . str_pad((string)($custModel->count() + 1), 4, '0', STR_PAD_LEFT);

        $id = $custModel->create([
            'customer_code' => $code,
            'name' => $name,
            'mobile' => $mobile,
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'city' => $request->input('city', 'Chennai'),
            'state' => $request->input('state', 'Tamil Nadu'),
            'pincode' => $request->input('pincode'),
            'gst_number' => $request->input('gst_number'),
            'credit_limit' => (float)$request->input('credit_limit', 0),
            'customer_type' => $request->input('customer_type', 'retail'),
            'status' => 'active'
        ]);

        $this->logActivity('create', 'customers', (int)$id, $code, "Registered customer {$name}");
        $newCustomer = $custModel->find((int)$id);

        Response::success('Customer registered successfully!', ['customer' => $newCustomer]);
    }

    public function update(Request $request, array $params): void {
        $this->requirePermission('customers.edit');
        $id = (int)($params['id'] ?? 0);
        $custModel = new Customer();
        $cust = $custModel->find($id);

        if (!$cust) {
            Response::error('Customer not found.');
        }

        $custModel->update($id, [
            'name' => $request->input('name'),
            'mobile' => $request->input('mobile'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'pincode' => $request->input('pincode'),
            'gst_number' => $request->input('gst_number'),
            'credit_limit' => (float)$request->input('credit_limit', 0),
            'customer_type' => $request->input('customer_type', 'retail'),
            'status' => $request->input('status', 'active')
        ]);

        $this->logActivity('update', 'customers', $id, $cust['customer_code'], "Updated customer {$cust['name']}");
        Response::success('Customer updated successfully.');
    }

    public function profile(Request $request, array $params): void {
        $this->requirePermission('customers.view');
        $id = (int)($params['id'] ?? 0);
        $custModel = new Customer();
        $customer = $custModel->find($id);

        if (!$customer) {
            Response::error('Customer not found.');
        }

        $invoices = $custModel->getInvoices($id, 15);
        $payments = $custModel->getPayments($id, 15);

        Response::json([
            'success' => true,
            'customer' => $customer,
            'invoices' => $invoices,
            'payments' => $payments
        ]);
    }
}
