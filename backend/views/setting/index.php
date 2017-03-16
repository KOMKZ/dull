<?php
use Yii;
use common\models\setting\SettingWidget;
use common\models\open\OpenModel;
use yii\bootstrap\Tabs;
/**
 * 3. 使用布局数组 后面再考虑分离
 */

$js = <<<JS
    $('#form').submit(function(){
        $.post($(this).attr('action'), $(this).serialize(), function(res){
            if(res.code > 0){
                return dull.new_alert(res.code + ':' + res.message, 'error');
            }else{
                return dull.new_alert('修改成功', 'success');
            }
        }, 'json')
        return false;
    });
JS;
$this->registerJs($js);
$layouts = [
    'web_area' => 'col-lg-3',
    'author_birthday' => 'col-lg-3'
];
$tabs = [];
foreach($settings as $item){
    $content = '<br/>';
    foreach($item['childrens'] as $setItem){
        if(!empty($layouts[$setItem['set_name']])){
            $width = $layouts[$setItem['set_name']];
            $tpl = "
            <div class='form-group'>
                <div class='row'><div class='%s'>
                    %s
                </div></div>
            </div>
            ";
            $content .= sprintf($tpl, $width, SettingWidget::render($setItem));
        }else{
            $tpl = "<div class='form-group'>%s</div>";
            $content .= sprintf($tpl, SettingWidget::render($setItem));
        }
    }
    $tabs[] = [
        'label' => $item['label'],
        'content' => $content
    ];
}
?>
<form id="form" class="" action="<?= $url['setting/update-all']?>" method="post">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <?php
                    echo Tabs::widget([
                        'items' => $tabs
                    ]);
                    ?>
                    <div class="form-group">
                        <input type="submit" name="" value="修改" class="btn btn-default">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">

        </div>
    </div>
</form>
