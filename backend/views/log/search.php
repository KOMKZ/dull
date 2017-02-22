<?php
use yii\grid\GridView;
use yii\log\Logger;
use yii\helpers\Html;
use yii\helpers\Url;
use common\assets\DateTimePickerAsset;
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
    function merge(target, source) {
        if ( typeof target !== 'object' ) {
            target = {};
        }
        for (var property in source) {
            if ( source.hasOwnProperty(property) ) {
                var sourceProperty = source[ property ];
                if ( typeof sourceProperty === 'object' ) {
                    target[ property ] = merge( target[ property ], sourceProperty );
                    continue;
                }
                target[ property ] = sourceProperty;
            }
        }
        for (var a = 2, l = arguments.length; a < l; a++) {
            merge(target, arguments[a]);
        }
        return target;
    };
    $('.view-log-btn').click(function(){
        $.get($(this).attr('href'), {
            table_name : $('#table_search').val()
        }, function(res){
            $('#log-detail').html(res);
        });
        return false;
    });
    $('#clear-log-detail').click(function(){
        $('#log-detail').html('');
    });
    $('#search-form').submit(function(){
        var query_params = yii.getQueryParams(location.href);
        var new_query_params = yii.getQueryParams('?' + $(this).serialize());
        var query_string = '';
        $.each( merge(query_params, new_query_params), function(i, v){
            query_string += i + '=' + v + '&';
        });
        query_string = query_string.substring(0, query_string.length - 1);
        location.href = $(this).attr('action') + '?' + query_string;
        return false;
    });

    $('#date_time_panel').DateTimePicker({
        dateTimeFormat: "yyyy-MM-dd HH:mm:ss",
        settingValueOfElement : function(sInputValue, dDateTime, oInputElement){
            $('[name='+$(oInputElement).attr('data-name')+']:visible').val(Date.parse(new Date(dDateTime))/1000);
        }
    });
    var query_params = yii.getQueryParams(location.href);
    $('#table_search').val(query_params['table_name']);
JS;
DateTimePickerAsset::register($this);
$this->registerJs($js);

?>

<div class="box">
    <p>
        <button type="button" id="clear-log-detail" class="btn btn-default">清空</button>
    </p>
    <pre class="box-body"  id="log-detail">
    </pre>
</div>
<div id="date_time_panel"></div>



<div class="row">
    <div class="col-md-9">
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
                    [
                        'attribute' => 'prefix',
                        'label' => '前缀'
                    ],

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
                            }else{
                                echo "
                                <div class='box-body'>没有数据</div>
                                ";
                            }
                    ?>
        </div>
    </div>
    <div class="col-md-3">
        <div class="box">
            <div class="box-body">
                <form class="" action="<?= $logSearchUrl?>" method="get" id="search-form">
                    <div class="form-group">
                        <label for="">日志表</label>
                        <select class="form-control" name="table_name" id="table_search">
                            <option value="log_backend">后台应用</option>
                            <option value="log_frontend">前台应用</option>
                            <option value="log_api">Api应用</option>
                            <option value="log_console">console应用</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">级别</label>
                        <input type="text" name="level" value="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">前缀（模糊）</label>
                        <input type="text" name="prefix[like]" value="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">类型（模糊）</label>
                        <input type="text" name="category[like]" value="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">开始时间</label>
                        <input type="text" data-field='datetime' name="log_time[start]" value="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">结束时间</label>
                        <input type="text"  data-field='datetime'  name="log_time[end]" value="" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="submit" name="" value="提交" class="btn btn-default">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
