<?php
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
if(Yii::$app->user->isGuest){
    $u_username = '';
}else{
    $u_username = Yii::$app->user->identity->u_username;
}
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

    $('#pull-form').submit(function(){
        $.post($(this).attr('action'), $(this).serialize(), function(res){
            if(res.code > 0){
                sweetAlert("", res.message, "error");
            }else{
                location.href = location.href;
            }
        }, 'json')
        return false;
    });

    $('.view-btn').click(function(){
        $.get($(this).attr('href'), {
            'um_id' : $(this).attr('data-id')
        }, function(res){
            if(res.code > 0){
                sweetAlert("", res.message, "error");
            }else{
                swal(res.data.um_title, res.data.um_content);
            }
        }, 'json')
        return false;
    });

    $('.set-read-btn').click(function(){
        $.get($(this).attr('href'), {
            'um_id' : $(this).attr('data-id')
        }, function(res){
            if(res.code > 0){
                sweetAlert("", res.message, "error");
            }else{
                location.href = location.href;
            }
        }, 'json')
        return false;
    });
    var query_params = yii.getQueryParams(location.href);
    if(query_params.um_read_status){
        $("select[name='um_read_status']").val(query_params.um_read_status);
    }

JS;
$this->registerJs($js);


?>
<div class="row">
    <div class="col-md-8">
        <div class="box">
            <?php
            if($provider){
                echo GridView::widget([
                    'dataProvider' => $provider,
                    'columns' => [
                        ['attribute' => 'um_id'],
                        ['attribute' => 'um_title'],
                        [
                            'attribute' => 'um_read_status',
                            'value' => function($model, $key, $index, $column) use($readStatusMap){
                                return $readStatusMap[$model['um_read_status']];
                            },
                        ],

                        [
                            'attribute' => 'um_created_at',
                            'value' => function($model, $key, $index, $column){
                                return date('Y-m-d H:i:s', $model['um_created_at']);
                            },
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{view}{set-read}',
                            'buttons' => [
                                'view' => function($url, $model, $key){
                                    return Html::a('查看', $url, [
                                        'class' => 'btn btn-primary btn-xs view-btn',
                                        'data-id' => $model['um_id']
                                    ]) . ' ';
                                },
                                'set-read' => function($url, $model, $key){
                                    return Html::a('设已读', $url, [
                                        'class' => 'btn btn-primary btn-xs set-read-btn',
                                        'data-id' => $model['um_id']
                                    ]) . ' ';
                                }
                            ],
                            'urlCreator' => function ($action, $model, $key, $index) use($getOneNotifyUrl, $setNotifyReadUrl){
                                switch ($action) {
                                    case 'view':
                                        return $getOneNotifyUrl;
                                    case 'set-read':
                                        return $setNotifyReadUrl;
                                }
                            },
                            'visibleButtons' => [
                                'set-read' => function ($model, $key, $index) {
                                    return 0 == $model['um_read_status'];
                                }
                            ],
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
    <div class="col-md-4">
        <div class="box">
            <div class="box-body">
                <form id="search-form" action="<?= $searchNotifyUrl?>" method="post">
                    <div class="form-group">
                        <label for="">状态</label>
                        <select class="form-control" name="um_read_status">
                            <option value="1,0">全部</option>
                            <option value="1">已读</option>
                            <option value="0">未读</option>
                        </select>
                    </div>
                    <div class="from-group">
                        <input type="submit" class="btn btn-default" value="查找">
                    </div>
                </form>
            </div>
        </div>
        <div class="box">
            <div class="box-body">
                <form id="pull-form" action="<?= $pullNotifyUrl?>" method="post">
                    <div class="form-group">
                        <input type="hidden" name="u_username" value="<?= $u_username;?>">
                        <input type="submit" name="" value="主动拉取" class="btn btn-default">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
