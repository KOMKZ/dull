<?php
use yii\grid\GridView;
use yii\log\Logger;
use yii\helpers\Html;
use yii\helpers\Url;
$levelMap = [
    'LEVEL_ERROR' => Logger::LEVEL_ERROR,
    'LEVEL_WARNING' => Logger::LEVEL_WARNING,
    'LEVEL_INFO' => Logger::LEVEL_INFO,
    'LEVEL_TRACE' => Logger::LEVEL_TRACE,
    'LEVEL_PROFILE' => Logger::LEVEL_PROFILE,
    'LEVEL_PROFILE_BEGIN' => Logger::LEVEL_PROFILE_BEGIN,
    'LEVEL_PROFILE_END' => Logger::LEVEL_PROFILE_END
];
$url = Url::to(['log/one']);
$js = <<<JS
    $('#table').val("{$tableName}");
    $('.view-log-btn').click(function(){
        $.get($(this).attr('href'), {
            'table_name' : $('#table').val()
        },function(res){
            $('#log-detail').html(res);
        });
        return false;
    });
    $('#clear-log-detail').click(function(){
        $('#log-detail').html('');
    });
JS;

$this->registerJs($js);
?>

<div class="box">
    <p>
        <button type="button" id="clear-log-detail" class="btn btn-default">清空</button>
    </p>
    <pre class="box-body"  id="log-detail">
    </pre>
</div>

<div class="box">
    <ul>
        <?php foreach ($levelMap as $key => $value): ?>
            <li><?= $key?> : <?= $value?></li>
        <?php endforeach; ?>
    </ul>
</div>

<form class="" method="post" id="search-form">
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">table_name</label>
                        <select class="form-control" name="table_name" id="table">
                            <option value="log_backend">后台应用</option>
                            <option value="log_frontend">前台应用</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">level</label>
                        <input type="text" name="level" class="form-control" value="">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">category</label>
                        <input type="text" name="category" class="form-control" value="">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">category[like]</label>
                        <input type="text" name="category[like]" class="form-control" value="">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">prefix[like]</label>
                        <input type="text" name="prefix[like]" class="form-control" value="">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <input type="submit" name="" value="提交" class="btn btn-default">
                </div>
            </div>
        </div>
    </div>
</form>


<div class="box">
    <?php
    if($provider){
        echo GridView::widget([
            'dataProvider' => $provider,
            'columns' => [
                [ 'attribute' => 'id', 'label' => '日志编号'],
                [ 'attribute' => 'level', 'label' => '日志等级',
                'value' => function($model, $key, $index, $column){
                    switch ($model['level']) {
                        case Logger::LEVEL_ERROR:
                        return 'LEVEL_ERROR';
                        case Logger::LEVEL_WARNING:
                        return 'LEVEL_WARNING';
                        case Logger::LEVEL_INFO:
                        return 'LEVEL_INFO';
                        case Logger::LEVEL_TRACE:
                        return 'LEVEL_TRACE';
                        case Logger::LEVEL_PROFILE:
                        return 'LEVEL_PROFILE';
                        case Logger::LEVEL_PROFILE_BEGIN:
                        return 'LEVEL_PROFILE_BEGIN';
                        case Logger::LEVEL_PROFILE_END:
                        return 'LEVEL_PROFILE_END';
                    }
                },
            ],
            ['attribute' => 'category', 'label' => '类型'],
            [
                'attribute' => 'log_time',
                'value' => function($model, $key, $index, $column){
                    list($s, $m) = explode('.', $model['log_time']);
                    return date('Y-m-d H:i:s', $s) . ' ' . $m;
                },
                'label' => '创建时间'
            ],
            // [
            //     'attribute' => 'od_price',
            //     'value' => function($model, $key, $index, $column){
            //         return '¥'.($model['od_price'] / 100);
            //     },
            //     'label' => '价格',
            // ],
            [
                'attribute' => 'prefix',
                'label' => '前缀'
            ],
            // [
            //     'attribute' => 'od_goods.0.good_source.0.format',
            //     'label' => '形式课程'
            // ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'buttonOptions' => ['target' => '_blank'],
                'template' => '{view}',
                'buttons' => [
                'view' => function($url, $model, $key){
                    return Html::a('查看', $url, [
                    'class' => 'btn btn-primary btn-xs view-log-btn',
                    'data-id' => $model['id']
                    ]);
                },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    switch ($action) {
                        case 'view':
                        return Url::to(['log/one', 'id' => $model['id']]);
                    }
                },
                ],
                ]
                ]);
    }
    ?>
</div>
