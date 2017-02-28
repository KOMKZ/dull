<?php
use common\models\user\tables\User;
use common\models\user\tables\UserGroup;




$requird_users = [
    [
        'User' => [
            'u_username' => 'admin',
            'password' => '123456',
            'password_confirm' => '123456',
            'u_status' => User::STATUS_ACTIVE,
            'u_auth_status' => User::STATUS_AUTHED,
            'u_email' => '784248377@qq.com'
        ],
        'UserIdentity' => [
            'ui_gid' => UserGroup::ROOT_GROUP,
        ]
    ]
];
$test_users = [];
$userData = ['User' => [], 'UserIdentity' => []];
$i = 0;
while($i < 3){
    $faker = \Faker\Factory::create();
    $userData['User'] = [
        'u_username' => strtolower($faker->firstName.'_'.$faker->lastName),
        'u_email' => $faker->email,
        'password' => '123456',
        'password_confirm' => '123456',
        'u_status' => User::STATUS_ACTIVE,
        'u_auth_status' => User::STATUS_AUTHED,
    ];
    $userData['UserIdentity'] = [
        'ui_gid' => UserGroup::TEST_GROUP
    ];
    $test_users[] = $userData;
    $i++;
}
return array_merge($requird_users, $test_users);
