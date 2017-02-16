<?php
use yii\widgets\DetailView;
use common\formatter\Formatter;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
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
                            'format' => ['date', 'Y-MM-dd HH-mm-ss']
                        ],
                        [
                            'attribute' => 'u_updated_at',
                            'format' => ['date', 'Y-MM-dd HH-mm-ss']
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
</div>
