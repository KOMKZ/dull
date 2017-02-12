<?php
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\user\tables\User;
use yii\bootstrap\ButtonDropdown;
$script = <<<JS
    function showalert(message, alerttype, container) {
        if($.isArray(message)){
            message = message.join("<br/>");
        }
        container = container ? container : '#alert';
        $(container).append('<div id="alertdiv" class="alert alert-' +  alerttype + '"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
        setTimeout(function() { // this will automatically close the alert and remove this if the users doesnt close it in 5 secs
            $("#alertdiv").remove();
        }, 10000);
    }

    $('.b_set_all_status').click(function(){
        var keys = $('#grid').yiiGridView('getSelectedRows');
        var status = $(this).attr('data-status');
        if(keys.length){
            $.post($(this).attr('href'), {
                'u_id' : keys,
                'u_status' : status
            }, function(res){
                if(res.code > 0){
                    showalert(res.messages[''], 'error');
                }else{
                    location.href = location.href;
                }
            }, 'json')
        }
        return false;
    });
JS;
$this->registerJs($script);
?>
<div class="row">
    <div class='col-md-12'>
        <div class="box">
            <div class="box-body">
                <?php
                echo ButtonDropdown::widget([
                    'label' => '状态修改',
                    'dropdown' => [
                        'items' => [
                            ['label' => '设置为删除', 'url' => $userSetStatusApi, 'linkOptions' => [
                                'class' => 'b_set_all_status',
                                'data-status' => User::STATUS_DELETE
                                ]],
                            ['label' => '设置为可用', 'url' => $userSetStatusApi, 'linkOptions' => [
                                'class' => 'b_set_all_status',
                                'data-status' => User::STATUS_ACTIVE
                                ]],
                            ['label' => '设置为锁定', 'url' => $userSetStatusApi, 'linkOptions' => [
                                'class' => 'b_set_all_status',
                                'data-status' => User::STATUS_LOCKED
                                ]],
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <?php
                if($provider){
                    echo GridView::widget([
                        'id' => 'grid',
                        'dataProvider' => $provider,
                        'columns' => [
                            [
                                'class' => 'yii\grid\CheckboxColumn'
                            ],
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
                                'attribute' => 'u_updated_at',
                                'value' => function($model, $key, $index, $column){
                                    return date('Y-m-d H:i:s', $model['u_updated_at']);
                                },
                            ],
                            [
                                'attribute' => 'u_status',
                                'value' => function($model, $key, $index, $column) use($userStatusMap){
                                    return $userStatusMap[$model['u_status']];
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
                                'template' => '{view} {update}',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    switch ($action) {
                                        case 'view':
                                            return Url::to(['user/view', 'u_id' => $model['u_id']]);
                                        case 'update':
                                            return Url::to(['user/update', 'u_id' => $model['u_id']]);
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
</div>
