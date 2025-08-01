<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomJsSettingsSeeder extends Seeder
{
    public function run()
    {
        $settingModel = new \App\Models\SettingModel();
        
        // Add custom JavaScript enabled setting
        $settingModel->setSetting('custom_js_enabled', false, 'boolean');
        
        // Add custom JavaScript footer setting
        $settingModel->setSetting('custom_js_footer', '', 'text');
        
        echo "Custom JavaScript settings added successfully!\n";
    }
}
