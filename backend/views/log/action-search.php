<?php
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="box">
    <pre class="box-body"  id="log-detail"></pre>
</div>
<div id="date_time_panel"></div>

<div class="row">
    <div class="col-md-9">
        <div class="box">
            <?php
            echo GridView::widget([
                'dataProvider' => $provider,
                'columns' => [
                    [ 'attribute' => 'al_id', 'label' => '日志编号'],
                    ['attribute' => 'al_action', 'label' => '类型'],
                    ['attribute' => 'al_summary', 'label' => '类型'],
                    [
                        'attribute' => 'al_created_time',
                        'value' => function($model, $key, $index, $column){
                            return date('Y-m-d H:i:s', $model['al_created_time']);
                        },
                        'label' => '创建时间'
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function($url, $model, $key){
                                return Html::a('查看', $url, [
                                    'class' => 'btn btn-primary btn-xs view-log-btn',
                                    'data-id' => $model['al_id']
                                    ]);
                                },
                                ],
                        'urlCreator' => function ($action, $model, $key, $index) {
                            switch ($action) {
                                case 'view':
                                return Url::to(['log/one', 'id' => $model['al_id']]);
                            }
                        },
                    ],
            ]
        ]);
            ?>
        </div>
    </div>
    <div class="col-md-3">
        <div class="box">

        </div>
    </div>
</div>
