<?php
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\helpers\Url;
use common\formatter\Formatter;
use common\models\user\tables\UserGroup;
use yii\bootstrap\Tabs;
?>
<div class="row">
    <div class="col-lg-6">
        <div class="box">
            <div class="box-body">
                <?php
                echo Tabs::widget([
                    'items' => [
                        [
                            'label' => '基本信息',
                            'active' => true,
                            'content' => DetailView::widget([
                                'model' => $model,
                                'attributes' => [
                                    'u_id',
                                    'u_username',
                                    'u_email',
                                    [
                                        'attribute' => 'u_status',
                                        'format' => ['map', $userStatusMap]
                                    ],
                                    [
                                        'attribute' => 'u_auth_status',
                                        'format' => ['map', $userAuthStatusMap]
                                    ],
                                    [
                                        'attribute' => 'u_created_at',
                                        'format' => ['date', 'Y-MM-dd HH:mm:ss']
                                    ],
                                    [
                                        'attribute' => 'u_updated_at',
                                        'format' => ['date', 'Y-MM-dd HH:mm:ss']
                                    ]
                                ],
                                'formatter' => [
                                    'class' => 'common\formatter\Formatter',
                                ]
                            ])
                        ],
                        [
                            'label' => '身份信息',
                            'active' => false,
                            'content' => DetailView::widget([
                                'model' => $model->identity,
                                'attributes' => [
                                    [
                                        'attribute' => 'group_info.ug_description',
                                    ]
                                ],
                                'formatter' => [
                                    'class' => 'common\formatter\Formatter'
                                ]
                            ])
                        ]
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="box">
            <div class="box-body">
                <?php
                echo Tabs::widget([
                    'items' => [
                        [
                            'label' => '粉丝列表',
                            'active' => true,
                            'content' => GridView::widget([
                                'id' => 'grid_fans',
                                'dataProvider' => $fansProvider,
                                'columns' => [
                                    [ 'attribute' => 'u_username'],
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'header' => '操作',
                                        // 'buttonOptions' => ['target' => '_blank'],
                                        'template' => '{view}',
                                        'urlCreator' => function ($action, $model, $key, $index) {
                                            switch ($action) {
                                                case 'view':
                                                return Url::to(['user/view', 'u_id' => $model['uf_uid']]);
                                            }
                                        },
                                    ]
                                ]
                            ])
                        ],
                        [
                            'label' => '关注列表',
                            'active' => false,
                            'content' => GridView::widget([
                                'id' => 'grid_focus',
                                'dataProvider' => $focusProvider,
                                'columns' => [
                                    [ 'attribute' => 'u_f_username'],
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'header' => '操作',
                                        // 'buttonOptions' => ['target' => '_blank'],
                                        'template' => '{view}',
                                        'urlCreator' => function ($action, $model, $key, $index) {
                                            switch ($action) {
                                                case 'view':
                                                return Url::to(['user/view', 'u_id' => $model['uf_f_uid']]);
                                            }
                                        },
                                    ]
                                ]
                            ])
                        ]
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>

</div>
