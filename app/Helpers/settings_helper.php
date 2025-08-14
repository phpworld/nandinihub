<?php

if (!function_exists('get_setting')) {
    /**
     * Get a setting value from the database with caching
     * 
     * @param string $key Setting key
     * @param mixed $default Default value if setting not found
     * @return mixed Setting value
     */
    function get_setting(string $key, $default = null)
    {
        static $cache = [];
        static $settingModel = null;
        
        // Initialize model if not already done
        if ($settingModel === null) {
            $settingModel = new \App\Models\SettingModel();
        }
        
        // Check cache first
        if (!isset($cache[$key])) {
            $cache[$key] = $settingModel->getSetting($key, $default);
        }
        
        return $cache[$key];
    }
}

if (!function_exists('get_all_settings')) {
    /**
     * Get all settings as an array with caching
     * 
     * @return array All settings
     */
    function get_all_settings(): array
    {
        static $allSettings = null;
        static $settingModel = null;
        
        // Initialize model if not already done
        if ($settingModel === null) {
            $settingModel = new \App\Models\SettingModel();
        }
        
        // Load all settings once
        if ($allSettings === null) {
            $allSettings = $settingModel->getAllSettings();
        }
        
        return $allSettings;
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format a price with the configured currency
     * 
     * @param float $amount Amount to format
     * @param bool $showSymbol Whether to show currency symbol
     * @return string Formatted currency string
     */
    function format_currency(float $amount, bool $showSymbol = true): string
    {
        $currency = get_setting('currency', 'USD');
        $symbol = get_setting('currency_symbol', '$');
        $position = get_setting('currency_position', 'before');
        
        $formattedAmount = number_format($amount, 2);
        
        if (!$showSymbol) {
            return $formattedAmount;
        }
        
        if ($position === 'after') {
            return $formattedAmount . $symbol;
        } else {
            return $symbol . $formattedAmount;
        }
    }
}

if (!function_exists('get_currency_symbol')) {
    /**
     * Get the configured currency symbol
     * 
     * @return string Currency symbol
     */
    function get_currency_symbol(): string
    {
        return get_setting('currency_symbol', '$');
    }
}

if (!function_exists('get_site_name')) {
    /**
     * Get the site name
     * 
     * @return string Site name
     */
    function get_site_name(): string
    {
        return get_setting('site_name', 'Nandini Hub');
    }
}

if (!function_exists('get_site_tagline')) {
    /**
     * Get the site tagline
     * 
     * @return string Site tagline
     */
    function get_site_tagline(): string
    {
        return get_setting('site_tagline', 'Your Trusted Shopping Destination');
    }
}

if (!function_exists('get_site_logo')) {
    /**
     * Get the site logo path
     * 
     * @return string Site logo path
     */
    function get_site_logo(): string
    {
        return get_setting('site_logo', '');
    }
}

if (!function_exists('clear_settings_cache')) {
    /**
     * Clear the settings cache (useful after updating settings)
     */
    function clear_settings_cache(): void
    {
        // Reset static variables in helper functions
        $reflection = new ReflectionFunction('get_setting');
        $staticVars = $reflection->getStaticVariables();
        foreach ($staticVars as $key => $value) {
            if ($key === 'cache') {
                $staticVars[$key] = [];
            }
        }
        
        $reflection = new ReflectionFunction('get_all_settings');
        $staticVars = $reflection->getStaticVariables();
        foreach ($staticVars as $key => $value) {
            if ($key === 'allSettings') {
                $staticVars[$key] = null;
            }
        }
    }
}

if (!function_exists('get_currency_options')) {
    /**
     * Get available currency options
     * 
     * @return array Currency options
     */
    function get_currency_options(): array
    {
        return [
            'USD' => ['name' => 'US Dollar', 'symbol' => '$'],
            'INR' => ['name' => 'Indian Rupee', 'symbol' => '₹'],
            'EUR' => ['name' => 'Euro', 'symbol' => '€'],
            'GBP' => ['name' => 'British Pound', 'symbol' => '£'],
            'CAD' => ['name' => 'Canadian Dollar', 'symbol' => 'C$'],
            'AUD' => ['name' => 'Australian Dollar', 'symbol' => 'A$'],
            'JPY' => ['name' => 'Japanese Yen', 'symbol' => '¥'],
        ];
    }
}

if (!function_exists('get_timezone_options')) {
    /**
     * Get available timezone options
     * 
     * @return array Timezone options
     */
    function get_timezone_options(): array
    {
        return [
            'Asia/Kolkata' => 'Asia/Kolkata (IST)',
            'UTC' => 'UTC',
            'America/New_York' => 'America/New_York (EST)',
            'America/Los_Angeles' => 'America/Los_Angeles (PST)',
            'Europe/London' => 'Europe/London (GMT)',
            'Europe/Paris' => 'Europe/Paris (CET)',
            'Asia/Tokyo' => 'Asia/Tokyo (JST)',
            'Australia/Sydney' => 'Australia/Sydney (AEST)',
        ];
    }
}

if (!function_exists('get_date_format_options')) {
    /**
     * Get available date format options
     * 
     * @return array Date format options
     */
    function get_date_format_options(): array
    {
        return [
            'd/m/Y' => 'DD/MM/YYYY',
            'm/d/Y' => 'MM/DD/YYYY',
            'Y-m-d' => 'YYYY-MM-DD',
            'd-m-Y' => 'DD-MM-YYYY',
            'M d, Y' => 'Mon DD, YYYY',
        ];
    }
}
