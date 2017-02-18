<?php
return [
    'app-backend-user' => [
        'name' => 'app-backend-user',
        'description' => '用户管理权限',
        'children' => [
            'app-backend-user/login' => [
                'name' => 'app-backend-user/login',
                'description' => '用户登录后台权限',
            ],
            'app-backend-user/logout' => [
                'name' => 'app-backend-user/logout',
                'description' => '用户登出后台权限',
            ],
            'app-backend-user/index' => [
                'name' => 'app-backend-user/index',
                'description' => '进入用户模块首页权限',
            ],
            'app-backend-user/group-list' => [
                'name' => 'app-backend-user/group-list',
                'description' => '获取用户组权限',
            ],
            'app-backend-user/group-view' => [
                'name' => 'app-backend-user/group-view',
                'description' => '查看某个用户组权限',
            ],
            'app-backend-user/group-update' => [
                'name' => 'app-backend-user/group-update',
                'description' => '更新某个用户组权限',
            ],
            'app-backend-user/list' => [
                'name' => 'app-backend-user/list',
                'description' => '获取用户列表权限',
            ],
            'app-backend-user/add' => [
                'name' => 'app-backend-user/add',
                'description' => '创建某个用户权限',
            ],
            'app-backend-user/update' => [
                'name' => 'app-backend-user/update',
                'description' => '更新某个用户权限',
            ],
            'app-backend-user/view' => [
                'name' => 'app-backend-user/view',
                'description' => '查看某个用户权限',
            ]
        ],
    ],
];
