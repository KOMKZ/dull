<?php
use yii\grid\GridView;
use yii\helpers\Url;

?>
<div class="row">

    <div class="col-md-12">
        <div class="box">
            <?php
            if($provider){
                echo GridView::widget([
                    'dataProvider' => $provider,
                    'formatter' => [
                        'class' => 'common\formatter\Formatter',
                    ],
                    'columns' => [
                        ['attribute' => 't_id'],
                        ['attribute' => 't_number'],
                        [ 'attribute' => 't_title'],
                        [
                            'attribute' => 't_fee',
                            'value' => function($model, $key, $index, $column){
                                return '¥ '.($model['t_fee'] / 100);
                            },
                        ],
                        [
                            'attribute' => 't_type',
                            'format' => ['map', $map['t_type']]
                        ],
                        [
                            'attribute' => 't_status',
                            'format' => ['map', $map['t_status']]
                        ],
                        [
                            'attribute' => 't_pay_status',
                            'format' => ['map', $map['t_pay_status']]
                        ],
                        [
                            'attribute' => 't_error_status',
                            'format' => ['map', $map['t_error_status']]
                        ],
                        [
                            'attribute' => 't_created_at',
                            'value' => function($model, $key, $index, $column){
                                return date('Y-m-d H:i:s', $model['t_created_at']);
                            },
                        ],
                        [
                            'attribute' => 't_updated_at',
                            'value' => function($model, $key, $index, $column){
                                return date('Y-m-d H:i:s', $model['t_updated_at']);
                            },
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            // 'buttonOptions' => ['target' => '_blank'],
                            'template' => '{view}{update}',
                            'urlCreator' => function ($action, $model, $key, $index) {
                                switch ($action) {
                                    case 'view':
                                        return Url::to(['trans/view', 'id' => $model['t_id']]);
                                    case 'update':
                                        return Url::to(['trans/update', 'id' => $model['t_id']]);
                                }
                            },
                        ]
                    ]
                ]);
            }else{
                echo "
                <div class='box-body'>没有数据</div>
                ";
            }
            ?>
        </div>
    </div>
</div>
