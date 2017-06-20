<?php
/**
 * tpl 中支持的 变量
 * uanme
 * al_uid
 * time
 * al_object_id
 * al_module
 * al_action
 * al_app_id
 * al_ip
 * al_agent_info
 * al_id
 */
return [
    //用户模块
    \common\models\log\ActionModel::M_USER => [
        'user_login' => [
            'des' => '用户登录',
            'tpl' => '%al_timestr% 用户(%al_uname%),ID为(%al_uid%) 登录 IP为(%al_ip%)'
        ],
        'user_logout' => [
            'des' => '用户登出',
            'tpl' => '%al_timestr% 用户(%al_uname%),ID为(%al_uid%) 登出 IP为(%al_ip%)'
        ]
    ]
];
