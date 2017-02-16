<?php
return [
    [
        'name' => 'root_role',
        'description' => '超级管理员角色',
        'permissions' => [
            'backend_admin' => ['*'],
        ],
    ],
    [
        'name' => 'admin_role',
        'description' => '普通管理员角色',
        'permissions' => [
            'backend_admin' => [
                'setting:index'
            ],
        ],
    ],
];