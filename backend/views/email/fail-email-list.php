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
                        [ 'attribute' => 'emf_id', 'label' => '编号'],
                        [ 'attribute' => 'emf_code', 'label' => '错误编号'],
                        [ 'attribute' => 'emf_message', 'label' => '描述'],
                        [
                            'attribute' => 'emf_created_at',
                            'label' => '生成时间',
                            'value' => function($model, $key, $index, $column){
                                return date('Y-m-d H:i:s', $model['emf_created_at']);
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
                                    return Url::to(['email/fail-email-view', 'id' => $model['emf_id']]);
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
