<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Setting;
use App\Models\Product;

class BarcodeController extends Controller {

    public function index(Request $request): void {
        if (!hasPermission('products.view') && !hasPermission('settings.manage') && !hasPermission('settings.view')) {
            $this->requirePermission('products.view');
        }

        $prefix = (string)getSetting('barcode_prefix', 'SS');
        $numberLength = (int)getSetting('barcode_number_length', 4);
        $separator = (string)getSetting('barcode_separator', '-');
        $autoAssign = (string)getSetting('barcode_auto_assign', '1');

        $prodModel = new Product();
        $totalProducts = (int)$prodModel->count();

        // Get 5 recent products
        $db = \App\Core\Database::getInstance();
        $recentProducts = $db->query(
            "SELECT id, name, sku, barcode, selling_price, current_stock, created_at 
             FROM `products` 
             WHERE status != 'inactive' 
             ORDER BY id DESC LIMIT 5"
        );

        // Generate 4 live sample barcodes based on current settings
        $samples = [];
        $min = (int)pow(10, max(2, $numberLength - 1));
        $max = (int)(pow(10, max(2, $numberLength)) - 1);
        for ($i = 0; $i < 4; $i++) {
            $samples[] = strtoupper($prefix) . $separator . mt_rand($min, $max);
        }

        $this->render('settings.barcode', [
            'pageTitle' => 'Barcode Customization & Settings',
            'prefix' => $prefix,
            'numberLength' => $numberLength,
            'separator' => $separator,
            'autoAssign' => $autoAssign,
            'samples' => $samples,
            'totalProducts' => $totalProducts,
            'recentProducts' => is_array($recentProducts) ? $recentProducts : []
        ]);
    }

    public function save(Request $request): void {
        if (!hasPermission('products.view') && !hasPermission('settings.manage')) {
            $this->requirePermission('products.view');
        }

        $prefix = strtoupper(trim((string)$request->input('barcode_prefix', 'SS')));
        // Sanitize prefix: alphanumeric and basic characters, max 12 chars
        $prefix = preg_replace('/[^A-Z0-9_\-]/', '', $prefix);
        if ($prefix === '') {
            $prefix = 'SS';
        }

        $numberLength = (int)$request->input('barcode_number_length', 4);
        if ($numberLength < 3 || $numberLength > 8) {
            $numberLength = 4;
        }

        $separator = (string)$request->input('barcode_separator', '-');
        // Allowed separators: -, _, /, or empty
        if (!in_array($separator, ['-', '_', '/', ''], true)) {
            $separator = '-';
        }

        $autoAssign = $request->input('barcode_auto_assign') ? '1' : '0';

        $settingModel = new Setting();
        $settingModel->set('barcode_prefix', $prefix, 'barcode', 'Default barcode starting prefix');
        $settingModel->set('barcode_number_length', (string)$numberLength, 'barcode', 'Random number digit length');
        $settingModel->set('barcode_separator', $separator, 'barcode', 'Separator between prefix and random digits');
        $settingModel->set('barcode_auto_assign', $autoAssign, 'barcode', 'Auto assign barcode on new product');

        $this->logActivity('update', 'barcode_settings', null, $prefix, "Updated barcode format to {$prefix}{$separator}XXXX ({$numberLength} digits)");

        $formatPreview = $prefix . $separator . str_repeat('X', $numberLength);

        if ($request->isAjax()) {
            Response::json([
                'success' => true,
                'message' => "Barcode settings saved successfully! New format: {$formatPreview}",
                'prefix' => $prefix,
                'numberLength' => $numberLength,
                'separator' => $separator,
                'autoAssign' => $autoAssign,
                'preview' => $formatPreview
            ]);
            return;
        }

        Session::flash('success', "Barcode settings saved successfully! Products will now use: {$formatPreview}");
        Response::redirect('/barcode-settings');
    }

    public function generateSample(Request $request): void {
        $prefix = strtoupper(trim((string)$request->input('prefix', getSetting('barcode_prefix', 'SS'))));
        $prefix = preg_replace('/[^A-Z0-9_\-]/', '', $prefix) ?: 'SS';

        $numberLength = (int)$request->input('length', getSetting('barcode_number_length', 4));
        if ($numberLength < 3 || $numberLength > 8) $numberLength = 4;

        $separator = (string)$request->input('separator', getSetting('barcode_separator', '-'));
        if (!in_array($separator, ['-', '_', '/', ''], true)) $separator = '-';

        $min = (int)pow(10, $numberLength - 1);
        $max = (int)(pow(10, $numberLength) - 1);
        $code = $prefix . $separator . mt_rand($min, $max);

        Response::json([
            'success' => true,
            'barcode' => $code,
            'prefix' => $prefix,
            'length' => $numberLength,
            'separator' => $separator
        ]);
    }
}
