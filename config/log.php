<?php
return [
    'default' => 'file',
    'channels' => [
        'file' => [
            'type' => 'File',
            'path' => '',
            'single' => false,
            'max_files' => 30,
            'format' => '[%s][%s] %s',
            'time_format' => 'c',
            'apart_level' => [],
        ],
    ],
];
