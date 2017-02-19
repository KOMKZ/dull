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
                        ['attribute' => 'p_id'],
                        [ 'attribute' => 'p_title'],
                        [
                            'attribute' => 'p_created_at',
                            'value' => function($model, $key, $index, $column){
                                return date('Y-m-d H:i:s', $model['p_created_at']);
                            },
                        ],
                        [
                            'attribute' => 'p_updated_at',
                            'value' => function($model, $key, $index, $column){
                                return date('Y-m-d H:i:s', $model['p_updated_at']);
                            },
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            // 'buttonOptions' => ['target' => '_blank'],
                            'template' => '{view}',
                            'urlCreator' => function ($action, $model, $key, $index) {
                                switch ($action) {
                                    case 'view':
                                    return Url::to(['post/view', 'id' => $model['p_id']]);
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
