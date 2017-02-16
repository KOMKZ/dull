<?php
use yii\widgets\DetailView;
use common\formatter\Formatter;
use common\widgets\AdSelectWidget;
use yii\helpers\Url;
use yii\helpers\Html;

$adSelectWidget = new AdSelectWidget([
    'selections' => $validRoles,
    'selected' => $assignedRoles,
    'uniqueKey' => 'name',
    'renderItem' => function($index, $item){
        $tds = Html::tag('td', Html::tag('a', $item['name'], [
            'href' => Url::to(['rbac/role-view', 'name' => $item['name']]),
            'target' => '_blank'
        ]));
        $tds .= Html::tag('td', $item['description']);
        return $tds;
    },
    'renderSelectedItem' => function($index, $item){
        $tds = Html::tag('td', $item['name']);
        $tds .= Html::tag('td', $item['description']);
        return $tds;
    },
    'selectTitle' => '可选角色',
    'selectedTitle' => '已选角色',
    // 'action' => $permissionAdminUrl
]);
$adSelectWidget->registerScript();
$groupName = $model['ug_name'];
$script = <<<EOT
var ads_config = {
    pk : 'name',
    container : '#group-role-admin',
    on_submit : function(new_items, rm_items, ad_select){
        $.post("$groupPmiAdminUrl", {
            new_items : new_items,
            rm_items : rm_items,
            assign_id : "$groupName",
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
new AddSelect(ads_config);
EOT;

$this->registerJs($script);


?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                <?php
                echo DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'ug_id',
                        'ug_name',
                        'ug_description',
                        [
                            'attribute' => 'ug_created_at',
                            'format' => ['date', 'Y-MM-dd HH-mm-ss']
                        ],
                        [
                            'attribute' => 'ug_updated_at',
                            'format' => ['date', 'Y-MM-dd HH-mm-ss']
                        ]
                    ],
                    'formatter' => [
                        'class' => 'common\formatter\Formatter',
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="box" id="group-role-admin">
        <div class="box-body">
            <?= $adSelectWidget->render();?>
        </div>
    </div>
</div>
