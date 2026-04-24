<?php

use think\facade\Db;

if (!function_exists('format_date')) {
    function format_date($timestamp)
    {
        if (empty($timestamp)) {
            return '--';
        }
        return date('Y-m-d H:i', is_numeric($timestamp) ? $timestamp : strtotime($timestamp));
    }
}

if (!function_exists('format_money')) {
    function format_money($amount)
    {
        return '¥' . number_format(floatval($amount), 2);
    }
}

if (!function_exists('get_config')) {
    function get_config($key, $default = null)
    {
        static $configs = null;
        
        if ($configs === null) {
            $configs = [];
            try {
                $configsFromCache = \think\facade\Cache::get('system_configs');
                if ($configsFromCache) {
                    $configs = $configsFromCache;
                } else {
                    $configList = \app\model\Config::select();
                    foreach ($configList as $config) {
                        $configs[$config->key] = $config->value;
                    }
                    \think\facade\Cache::set('system_configs', $configs, 3600);
                }
            } catch (\Exception $e) {
                try {
                    $configList = \app\model\Config::select();
                    foreach ($configList as $config) {
                        $configs[$config->key] = $config->value;
                    }
                } catch (\Exception $ex) {
                    $configs = [];
                }
            }
        }
        
        return isset($configs[$key]) ? $configs[$key] : $default;
    }
}
