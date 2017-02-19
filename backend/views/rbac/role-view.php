<?php
use Yii;
use yii\widgets\DetailView;
use common\formatter\Formatter;
use common\widgets\AdSelectWidget;
use yii\helpers\Html;
$adSelectWidget = new AdSelectWidget([
    'selections' => $permissions,
    'selected' => $assignPermissions,
    'uniqueKey' => 'name',
    'renderItem' => function($index, $item){
        $tds = Html::tag('td', $item['name']);
        $tds .= Html::tag('td', $item['description']);
        return $tds;
    },
    'renderSelectedItem' => function($index, $item){
        $tds = Html::tag('td', $item['name']);
        $tds .= Html::tag('td', $item['description']);
        return $tds;
    },
    'selectTitle' => '可选权限',
    'selectedTitle' => '已选权限',
    'action' => $permissionAdminUrl
]);
$adSelectWidget->registerScript();
$roleName = $model['name'];
$script = <<<EOT
var ads_config = {
    pk : 'name',
    container : '#permissions-admin',
    on_submit : function(new_items, rm_items, ad_select){
        $.post("$permissionAdminUrl", {
            new_items : new_items,
            rm_items : rm_items,
            role_name : "$roleName"
        }, function(res){
            if(res.code > 0){
                dull.new_alert(res.message, 'error');

            }else{
                ad_select.init()
                dull.new_alert(res.message, 'success');
            }
        });
    }
};
var adSelect = new AddSelect(ads_config);


$('#del-role-btn').click(function(){
    var select = 0;
    if(C_ALERT_WHEN_DEL){
        select = confirm('Are you sure to remove this role');
    }else{
        select = 1;
    }
    if(select){
        $.post("$deleteRoleUrl", {
            'name' : $(this).attr('data-name')
        }, function(res){
            if(res.code > 0){
                $.fn.bubble.success('delete role successfully');
            }else{
                $.fn.bubble.error('something error', res.message);
            }
        });
    }
    return false;
});
$('#update-role-form').submit(function(){
    $.post($(this).attr('action'), $(this).serialize(), function(res){
        if(res.code > 0){
            $.fn.bubble.success('update successfully', res.data);
        }else{
            $.fn.bubble.error('something wrong', res.message);
        }
    });
    return false;
});
EOT;
$this->registerJs($script);



?>

<div class="row">
    <div class="box">
        <div class="box-body">
            <?php
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'name',
                    'description',
                    'rule_name',
                    'data',
                    [
                        'attribute' => 'type',
                        'format' => ['map', $itemTypeMap]
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => ['date', 'Y-MM-dd HH:mm:ss']
                    ],
                    [
                        'attribute' => 'updated_at',
                        'format' => ['date', 'Y-MM-dd HH:mm:ss']
                    ],
                ],
                'formatter' => [
                    'class' => 'common\formatter\Formatter',
                ]
            ]);
            ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="box" id="permissions-admin">
        <div class="box-body">
            <?= $adSelectWidget->render();?>
        </div>
    </div>
</div>
