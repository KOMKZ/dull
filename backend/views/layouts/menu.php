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
            'label' => '内容管理',
            'icon' => 'fa fa-share',
            'url' => '#',
            'items' => [
                [
                    'label' => '博文管理',
                    'icon' => 'fa fa-circle-o',
                    'items' => [
                        [
                            'label' => '博文列表',
                            'icon' => 'fa fa-circle-o',
                            'url' => ['/post/list']
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

            ],
        ],

        [
            'label' => '权限管理',
            'icon' => 'fa fa-share',
            'url' => '#',
            'items' => [
                [
                    'label' => '日志查询',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/rbac/index']
                ],
            ],
        ],

        [
            'label' => '邮件管理',
            'icon' => 'fa fa-share',
            'url' => '#',
            'items' => [
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
                    'label' => '文件查询',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/file/search']
                ],
            ],
        ],

        [
            'label' => '消息管理',
            'icon' => 'fa fa-share',
            'url' => '#',
            'items' => [
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
            ],
        ],


    ],
];
