<?php
return [
    'options' => ['class' => 'sidebar-menu'],
    'items' => [
        ['label' => 'Dull-Blog', 'options' => ['class' => 'header'], 'url' => ['/site']],
        ['label' => 'Debug', 'icon' => 'fa fa-dashboard', 'url' => ['/debug']],
        [
            'label' => '设置管理',
            'icon' => 'fa fa-share',
            'url' => '#',
            'items' => [
                [
                    'label' => '网站设置',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/setting/index']
                ],
            ],
        ],
        [
            'label' => '商品管理',
            'icon' => 'fa fa-share',
            'url' => '#',
            'items' => [
                [
                    'label' => '商品列表',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/goods/list']
                ],
                [
                    'label' => '商品查看',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/goods/view']
                ],
                [
                    'label' => '商品更新',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/goods/update']
                ],
                [
                    'label' => '商品增加',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/goods/add']
                ],
            ],
        ],

        [
            'label' => '内容管理',
            'icon' => 'fa fa-share',
            'url' => '#',
            'items' => [
                [
                    'label' => '文章管理',
                    'icon' => 'fa fa-circle-o',
                    'items' => [
                        [
                            'label' => '文章列表',
                            'icon' => 'fa fa-circle-o',
                            'url' => ['/post/list']
                        ],
                        [
                            'label' => '文章查看',
                            'icon' => 'fa fa-circle-o',
                            'url' => ['/post/view'],
                            'visible' => Yii::$app->requestedRoute == 'post/view'
                        ],
                        [
                            'label' => '文章更新',
                            'icon' => 'fa fa-circle-o',
                            'url' => ['/post/update'],
                            'visible' => Yii::$app->requestedRoute == 'post/update'
                        ],
                        [
                            'label' => '文章增加',
                            'icon' => 'fa fa-circle-o',
                            'url' => ['/post/add']
                        ],
                    ],
                ],
            ],
        ],

        [
            'label' => '用户管理',
            'icon' => 'fa fa-share',
            'url' => '#',
            'items' => [
                [
                    'label' => '用户列表',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/user/list']
                ],
                [
                    'label' => '用户组列表',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/user/group-list']
                ],
                [
                    'label' => '用户组查看',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/user/group-view'],
                    'visible' => Yii::$app->requestedRoute == 'user/group-view'
                ],
                [
                    'label' => '用户组修改',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/user/group-update'],
                    'visible' => Yii::$app->requestedRoute == 'user/group-update'
                ],
                [
                    'label' => '新增用户',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/user/add']
                ],
                [
                    'label' => '用户查看',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/user/view'],
                    'visible' => Yii::$app->requestedRoute == 'user/view',
                ],
                [
                    'label' => '用户修改',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/user/update'],
                    'visible' => Yii::$app->requestedRoute == 'user/update',
                ],

            ],
        ],

        [
            'label' => '权限管理',
            'icon' => 'fa fa-share',
            'url' => '#',
            'items' => [
                [
                    'label' => '权限列表',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/rbac/index']
                ],
                [
                    'label' => '角色列表',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/rbac/roles']
                ],
                [
                    'label' => '角色查看',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/rbac/role-view'],
                    'visible' => Yii::$app->requestedRoute == 'rbac/role-view'
                ],
            ],
        ],

        [
            'label' => '邮件管理',
            'icon' => 'fa fa-share',
            'url' => '#',
            'items' => [
                [
                    'label' => '我的邮箱',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/email/index']
                ],
                [
                    'label' => '发送邮件',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/email/send']
                ],
                [
                    'label' => '失败列表',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/email/fail-email-list']
                ],
            ],
        ],

        [
            'label' => '文件管理',
            'icon' => 'fa fa-share',
            'url' => '#',
            'items' => [
                [
                    'label' => '文件列表',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/file/list']
                ],
                [
                    'label' => '增加文件',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/file/add']
                ],
                [
                    'label' => '查看文件',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/file/file-view'],
                    'visible' => Yii::$app->requestedRoute == 'file/file-view'
                ],
            ],
        ],

        [
            'label' => '消息管理',
            'icon' => 'fa fa-share',
            'url' => '#',
            'items' => [
                [
                    'label' => '通知列表',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/notify/index']
                ],
                [
                    'label' => '发送通知',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/notify/send']
                ],
            ],
        ],


        [
            'label' => '日志管理',
            'icon' => 'fa fa-share',
            'url' => '#',
            'items' => [
                [
                    'label' => '日志查询',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/log/search']
                ],
            ],
        ],

        [
            'label' => '监控管理',
            'icon' => 'fa fa-share',
            'url' => '#',
            'items' => [
                [
                    'label' => '队列监控',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/monitor/index']
                ],
            ],
        ],

        [
            'label' => '工具管理',
            'icon' => 'fa fa-share',
            'url' => '#',
            'items' => [
                [
                    'label' => 'webshell',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/webshell']
                ],
                [
                    'label' => 'player',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/tool/player']
                ],
                // [
                //     'label' => '分词',
                //     'icon' => 'fa fa-circle-o',
                //     'url' => ['/tool/word-seg']
                // ],
            ],
        ],


    ],
];
