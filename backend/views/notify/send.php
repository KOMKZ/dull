<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap\Tabs;

$js = <<<JS
    $('#tpl_dropdown').change(function(){
        var val = $(this).val();
        $('#tpl_content').html($('#tpl_' + val).html());
        $('#tpl_title').html($('#tpl_title_' + val).html());
    });
    $('#tpl_dropdown').change();

JS;
$this->registerJs($js);
$form = ActiveForm::begin();
?>
<div class="row">
    <div class="col-lg-6">
        <div class="box">
            <div class="box-header with-border">
                <h5>消息编辑</h5>
            </div>
            <div class="box-body">
                <?php
                echo $form->field($model, 'sm_title');
                echo $form->field($model, 'sm_content');
                echo $form->field($model, 'sm_use_tpl')->dropDownList($mUseTplMap);
                echo $form->field($model, 'sm_tpl_type')->dropDownList($mTplTypeMap, ['id' => 'tpl_dropdown']);
                echo $form->field($model, 'sm_object_type')->dropDownList($mSmObjectTypeMap);
                echo $form->field($model, 'sm_object_id');
                ?>
                <div class="form-group">
                    <input type="submit" name="" value="提交" class="btn btn-default">
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="box">
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
                        echo Html::tag('p', Html::encode($item['title']), [
                            'id' => 'tpl_title_' . $item['value'],
                            'style' => 'display:none;'
                        ]);
                    }
                    ?>
                </div>
                <pre id="tpl_title"></pre>
                <pre id="tpl_content">

                </pre>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end();?>
