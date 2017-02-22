<?php
use common\models\user\tables\UserGroup;
return [
    [
        'ug_id' => UserGroup::ROOT_GROUP,
        'ug_name' => 'root_group',
        'ug_description' => '系统组（root）',
        'ug_created_at' => time(),
        'ug_updated_at' => time()
    ],
    [
        'ug_id' => UserGroup::ADMIN_GROUP,
        'ug_name' => 'admin_group',
        'ug_description' => '管理员组',
        'ug_created_at' => time(),
        'ug_updated_at' => time()
    ],
    [
        'ug_id' => UserGroup::TEST_GROUP,
        'ug_name' => 'test_group',
        'ug_description' => '测试用户组',
        'ug_created_at' => time(),
        'ug_updated_at' => time()
    ],
    [
        'ug_id' => UserGroup::VISTOR_GROUP,
        'ug_name' => 'vistor_group',
        'ug_description' => '游客组',
        'ug_created_at' => time(),
        'ug_updated_at' => time()
    ],
];
