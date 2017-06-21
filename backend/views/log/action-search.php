<?php
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use common\assets\DateTimePickerAsset;

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
    $('#date_time_panel').DateTimePicker({
        dateTimeFormat: "yyyy-MM-dd HH:mm:ss",
        settingValueOfElement : function(sInputValue, dDateTime, oInputElement){
            $('[name='+$(oInputElement).attr('data-name')+']:visible').val(Date.parse(new Date(dDateTime))/1000);
        }
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

    var params = yii.getQueryParams(location.href);
    if(params['al_action']){
        $('#search-form').find("[name=al_action]").val(params['al_action']);
    }

JS;
DateTimePickerAsset::register($this);
$this->registerJs($js);
?>

<div id="date_time_panel"></div>

<div class="row">
    <div class="col-md-9">
        <div class="box">
            <div class="box-body">
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
    </div>
    <div class="col-md-3">
        <div class="box">
            <div class="box-body">
                <form id="search-form" action="<?= $logSearchUrl?>" method="">
                    <div class="form-group">
                        <label for="">动作名称</label>
                        <?php
                        echo Html::dropDownList('al_action', null, $labels, ['class' => 'form-control']);
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="">对象id</label>
                        <input type="text" name="al_uid" value="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">对象id</label>
                        <input type="text" name="al_object_id" value="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">开始时间</label>
                        <input type="text" data-field='datetime' name="al_created_time[start]" value="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">结束时间</label>
                        <input type="text"  data-field='datetime'  name="al_created_time[end]" value="" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="submit" name="" value="查询" class="btn btn-default">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
