<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap\Tabs;
use budyaga\cropper\Widget;
use common\assets\CKEditorAsset;
CKEditorAsset::register($this);

$fileUploadedCallback = <<<JS
    function(name, data){
        if(!$.isPlainObject(data)){
            return dull.new_alert('上传失败', 'error');
        }else{
            if(data.code > 0){
                return dull.new_alert(data.code + ':' + data.message, 'error');
            }else{
                $('#post-p_thumb_img').val(data.file_name);
            }
        }
    }
JS;



$script = <<<JS
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replace('#editor1', {
        filebrowserBrowseUrl : 'http://www.baidu.com',
        filebrowserUploadUrl : '{$contentImgUploadUrl}'
    });
JS;
$this->registerJs($script);

$form = ActiveForm::begin();
$postBaseInfo  = $form->field($model, 'p_title')->textInput(['autofocus' => true]);
$postBaseInfo .= $form->field($model, 'p_content_type')->dropDownList($postContentTypeMap);
$postBaseInfo .= $form->field($model, 'p_status')->dropDownList($postStatusMap);
$postBaseInfo = Html::tag('div', $postBaseInfo, ['class' => 'box-body']);

$postThumbImg = Html::tag('div', $form->field($model, 'p_thumb_img')->widget(Widget::className(), [
        'uploadUrl' => $fileUploadUrl,
        'onCompleteJcrop' => $fileUploadedCallback
    ]), ['class' => 'box-body']);

$postContent = Html::tag('div', $form->field($model, 'p_content')->textarea(['id' => '#editor1']), ['class' => 'box-body']);

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
                        'content' => "<div class=\"row\"><div class=\"col-lg-6\">$postContent</div></div>",
                    ]
                ]
            ]);
            ?>
        </div>
    </div>
</div>

<?php ActiveForm::end();?>
