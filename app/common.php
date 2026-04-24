<?php

use think\facade\Db;

if (!function_exists('format_date')) {
    function format_date($timestamp)
    {
        if (empty($timestamp)) {
            return '--';
        }
        return date('Y-m-d H:i', $timestamp);
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
            $configs = \think\facade\Cache::get('system_configs');
            if (!$configs) {
                $configList = \app\model\Config::select();
                $configs = [];
                foreach ($configList as $config) {
                    $configs[$config->key] = $config->value;
                }
                \think\facade\Cache::set('system_configs', $configs, 3600);
            }
        }
        
        return isset($configs[$key]) ? $configs[$key] : $default;
    }
}
