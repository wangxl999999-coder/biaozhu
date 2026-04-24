<?php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'type' => 'mysql',
            'hostname' => env('hostname', '127.0.0.1'),
            'database' => env('database', 'biaozhu'),
            'username' => env('username', 'root'),
            'password' => env('password', ''),
            'hostport' => env('hostport', '3306'),
            'charset' => 'utf8mb4',
            'prefix' => env('prefix', 'bz_'),
            'debug' => env('app_debug', true),
        ],
    ],
];
