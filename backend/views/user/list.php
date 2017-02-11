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
                        [ 'attribute' => 'u_id'],
                        [ 'attribute' => 'u_username'],
                        [ 'attribute' => 'u_email'],
                        [
                            'attribute' => 'u_created_at',
                            'value' => function($model, $key, $index, $column){
                                return date('Y-m-d H:i:s', $model['u_created_at']);
                            },
                        ],
                        [
                            'attribute' => 'u_status',
                            'value' => function($model, $key, $index, $column){
                                return $model['u_status'];
                            },
                        ],
                        [
                            'attribute' => 'u_auth_status',
                            'value' => function($model, $key, $index, $column) use($userAuthStatusMap){
                                return $userAuthStatusMap[$model['u_auth_status']];
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
                                    return Url::to(['user/view', 'u_id' => $model['u_id']]);
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
