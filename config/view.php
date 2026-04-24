<?php
return [
    'type' => 'Think',
    'auto_rule' => 1,
    'view_dir_name' => 'view',
    'view_suffix' => 'html',
    'view_depr' => DIRECTORY_SEPARATOR,
    'tpl_begin' => '{',
    'tpl_end' => '}',
    'taglib_begin' => '{',
    'taglib_end' => '}',
    'tpl_cache' => false,
    'tpl_replace_string' => [
        '__STATIC__' => '/static',
        '__JS__' => '/static/js',
        '__CSS__' => '/static/css',
        '__IMG__' => '/static/images',
    ],
];
