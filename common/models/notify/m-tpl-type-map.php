<?php
return [
    'register' => [
        'value' => 'register',
        'label' => '注册通知',
        'title' => '欢迎注册DULL软件',
        'content' => "尊敬的用户 {username}, 欢迎您成为DULL软件的用户！",
    ],
    'publist_post' => [
        'value' => 'publist_post',
        'label' => '用户发表了文章',
        'title' => 'DULL软件您关注的用户发表了新的文章',
        'content' => "尊敬的用户 {username}, 您关注的用户 {focus_username} 发表了文章 <a href=\"{post_url}\"> {post_title} </a>"
    ]
];
