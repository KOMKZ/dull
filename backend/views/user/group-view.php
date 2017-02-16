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
                        'ug_id',
                        'ug_name',
                        'ug_description',
                        [
                            'attribute' => 'ug_created_at',
                            'format' => ['date', 'Y-MM-dd HH-mm-ss']
                        ],
                        [
                            'attribute' => 'ug_updated_at',
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
