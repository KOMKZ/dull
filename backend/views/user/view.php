<?php
use yii\widgets\DetailView;
use common\formatter\Formatter;
use common\models\user\tables\UserGroup;
?>
<div class="row">
    <div class="col-lg-6">
        <div class="box">
            <div class="box-header with-border">
                <h5>基本信息</h5>
            </div>
            <div class="box-body">
                <?php
                echo DetailView::widget([
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
                ]);
                ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="box">
            <div class="box-header with-border">
                <h5>身份信息</h5>
            </div>
            <div class="box-body">
                <?php
                echo DetailView::widget([
                    'model' => $model->identity,
                    'attributes' => [
                        [
                            'attribute' => 'group_info.ug_description',
                        ]
                    ],
                    'formatter' => [
                        'class' => 'common\formatter\Formatter'
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
