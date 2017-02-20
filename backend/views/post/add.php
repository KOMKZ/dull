<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap\Tabs;
use budyaga\cropper\Widget;



$fileUploaddOneCallBack = <<<JS
    function(e, data){
        if(!$.isPlainObject(data.result)){
            return dull.new_alert('上传失败', 'error');
        }else{
            if(data.result.code > 0){
                return dull.new_alert(data.result.code + ':' + data.result.message, 'error');
            }
        }
    }
JS;


$form = ActiveForm::begin();
$postBaseInfo  = $form->field($model, 'p_title')->textInput(['autofocus' => true]);
$postBaseInfo .= $form->field($model, 'p_content_type')->dropDownList($postContentTypeMap);
$postBaseInfo .= $form->field($model, 'p_status')->dropDownList($postStatusMap);
$postBaseInfo = Html::tag('div', $postBaseInfo, ['class' => 'box-body']);

$postThumbImg = Html::tag('div', $form->field($model, 'p_thumb_img')->widget(Widget::className(), [
        'uploadUrl' => $fileUploadUrl,
        'onCompleteJcrop' => "
        function(name, data){
            if(!$.isPlainObject(data)){
                return dull.new_alert('上传失败', 'error');
            }else{
                if(data.code > 0){
                    return dull.new_alert(data.code + ':' + data.message, 'error');
                }
            }
        }
        "
    ]), ['class' => 'box-body']);

$postContent = Html::tag('div', $form->field($model, 'p_content'), ['class' => 'box-body']);

?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                <?= Html::submitButton('添加', ['class' => 'btn btn-primary']);?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php
            echo Tabs::widget([
                'items' => [
                    [
                        'label' => '文章基本信息',
                        'active' => true,
                        'content' => "<div class=\"row\"><div class=\"col-lg-6\">$postBaseInfo</div></div>"
                    ],
                    [
                        'label' => '文章封面图信息',
                        'active' => false,
                        'content' => $postThumbImg
                    ],
                    [
                        'label' => '文章内容',
                        'active' => false,
                        'content' => "<div class=\"row\"><div class=\"col-lg-6\">$postBaseInfo</div></div>",
                    ]
                ]
            ]);
            ?>
        </div>
    </div>
</div>

<?php ActiveForm::end();?>
