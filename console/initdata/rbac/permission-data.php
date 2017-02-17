<?php
return [
    'backend_admin' => [
        'name' => 'backend_admin',
        'description' => '后台管理权限',
        'children' => [
            'user/list' => [
                'name' => 'user/list',
                'description' => '获取用户列表权限',
            ],
        ],
    ],
];
