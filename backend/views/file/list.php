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
                        [ 'attribute' => 'f_id'],
                        [ 'attribute' => 'f_name'],
                        [ 'attribute' => 'f_ext'],
                        [ 'attribute' => 'f_size', 'format' => ['shortSize']],
                        [
                            'attribute' => 'f_status',
                            'value' => function($model, $key, $index, $column) use($fileStatusMap){
                                return $fileStatusMap[$model['f_status']];
                            },
                        ],
                        [
                            'attribute' => 'f_storage_type',
                            'value' => function($model, $key, $index, $column) use($fileStorageTypeMap){
                                return $fileStorageTypeMap[$model['f_storage_type']];
                            },
                        ],
                        [
                            'attribute' => 'f_created_at',
                            'value' => function($model, $key, $index, $column){
                                return date('Y-m-d H:i:s', $model['f_created_at']);
                            },
                        ],
                        [
                            'attribute' => 'f_updated_at',
                            'value' => function($model, $key, $index, $column){
                                return date('Y-m-d H:i:s', $model['f_updated_at']);
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
                                    return Url::to(['file/file-view', 'id' => $model['f_id']]);
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