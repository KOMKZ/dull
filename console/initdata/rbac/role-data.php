<?php
return [
    [
        'name' => 'root_role',
        'description' => '超级管理员角色',
        'permissions' => [
            'app-backend-user' => ['*'],
        ],
    ],
    [
        'name' => 'admin_role',
        'description' => '普通管理员角色',
        'permissions' => [
            'app-backend-user' => [
                'app-backend-user/login'
            ],
        ],
    ],
    [
        'name' => 'vistor_role',
        'description' => '游客角色',
        'permissions' => [

        ],
    ],
];
