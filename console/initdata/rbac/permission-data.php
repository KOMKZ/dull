<?php
return [
    'backend_admin' => [
        'name' => 'backend_admin',
        'description' => '后台管理显示权限',
        'children' => [
            'setting:index' => [
                'name' => 'setting:index',
                'description' => '系统设置模块显示权限',
            ],
        ],
    ],
];
