<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap\Tabs;

$js = <<<JS
    $('#tpl_dropdown').change(function(){
        var val = $(this).val();
        $('#tpl_content').html($('#tpl_' + val).html());
    });
    $('#tpl_dropdown').change();

JS;
$this->registerJs($js);

?>
<div class="box">
    <div class="row">
        <div class="col-lg-6">
            <div class="box">
                <div class="box-header with-border">
                    <h5>消息编辑</h5>
                </div>
                <div class="box-body">
                    <?php
                    $form = ActiveForm::begin();
                    echo $form->field($model, 'm_title');
                    echo $form->field($model, 'tpl_type')->dropDownList($mTplTypeMap, ['id' => 'tpl_dropdown']);
                    ActiveForm::end();
                    ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="box-header with-border">
                <h5>模板内容</h5>
            </div>
            <div class="box-body">
                <div>
                    <?php
                        foreach ($mTplTypeData as $item) {
                            echo Html::tag('p', Html::encode($item['content']), [
                                'id' => 'tpl_'.$item['value'],
                                'style' => 'display:none;'
                            ]);
                        }
                    ?>
                </div>
                <pre id="tpl_content">

                </pre>
            </div>
        </div>
    </div>
</div>
