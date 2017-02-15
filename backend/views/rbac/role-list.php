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
                        [ 'attribute' => 'name'],
                        [ 'attribute' => 'description'],
                        [
                            'attribute' => 'created_at',
                            'value' => function($model, $key, $index, $column){
                                return date('Y-m-d H:i:s', $model['created_at']);
                            },
                        ],
                        [
                            'attribute' => 'updated_at',
                            'value' => function($model, $key, $index, $column){
                                return date('Y-m-d H:i:s', $model['updated_at']);
                            },
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'buttonOptions' => ['target' => '_blank'],
                            'template' => '{view}',
                            'urlCreator' => function ($action, $model, $key, $index) {
                                switch ($action) {
                                    case 'view':
                                    return Url::to(['rbac/role-view', 'name' => $model['name']]);
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
