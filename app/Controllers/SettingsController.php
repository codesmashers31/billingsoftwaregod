<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Setting;

class SettingsController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('settings.manage');
        $settingModel = new Setting();
        $groupedSettings = $settingModel->getAllGrouped();

        $this->render('settings.index', [
            'pageTitle' => 'Enterprise Settings & Customization',
            'settings' => $groupedSettings
        ]);
    }

    public function update(Request $request): void {
        $this->requirePermission('settings.manage');
        $settingModel = new Setting();
        $all = $request->all();

        foreach ($all as $key => $value) {
            if ($key === '_csrf_token' || str_starts_with($key, '_')) continue;
            $settingModel->set($key, $value);
        }

        $this->logActivity('update', 'settings', null, 'SYSTEM', 'Updated system and enterprise settings');

        if ($request->isAjax()) {
            Response::success('Settings updated successfully!');
        }

        Session::flash('success', 'Settings updated successfully.');
        Response::redirect('/settings');
    }
}
