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
                    'columns' => [
                        [ 'attribute' => 'ug_id'],
                        [ 'attribute' => 'ug_name'],
                        [ 'attribute' => 'ug_description'],
                        [
                            'attribute' => 'ug_created_at',
                            'value' => function($model, $key, $index, $column){
                                return date('Y-m-d H:i:s', $model['ug_created_at']);
                            },
                        ],
                        [
                            'attribute' => 'ug_updated_at',
                            'value' => function($model, $key, $index, $column){
                                return date('Y-m-d H:i:s', $model['ug_updated_at']);
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
                                        return Url::to(['user/group-view', 'name' => $model['ug_name']]);
                                    case 'update':
                                        return Url::to(['user/group-update', 'name' => $model['ug_name']]);
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
