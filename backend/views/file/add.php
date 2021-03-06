<?php
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use dosamigos\fileupload\FileUploadUI;
$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
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
$css = <<<CSS
.preview video{
    width:300px;
    height:300px;
}
CSS;
$this->registerCss($css);
?>
<div class="row">
    <div class="col-lg-5">
        <div class="box">
            <div class="box-body">
                <?php
                echo $form->field($model, 'f_name')->textInput(['autofocus' => true]);
                echo $form->field($model, 'f_prefix')->textInput();
                // echo $form->field($model, 'source_path_type')->dropDownList($filePathTypeMap);
                echo $form->field($model, 'f_storage_type')->dropDownList($map['f_storage_type']);
                echo $form->field($model, 'f_category')->dropDownList($map['f_category']);
                echo $form->field($model, 'f_acl_type')->dropDownList($map['f_acl_type']);
                echo $form->field($model, 'save_asyc')->dropDownList($map['save_asyc']);
                echo $form->field($model, 'f_valid_type')->dropDownList($map['f_valid_type']);
                // echo $form->field($model, 'u_email');
                // echo $form->field($model, 'u_status')->dropDownList($userStatusMap);
                // echo $form->field($model, 'u_auth_status')->dropDownList($userAuthStatusMap);
                // echo $form->field($model, 'password')->passwordInput();
                // echo $form->field($model, 'password_confirm')->passwordInput();
                // echo Html::submitButton('添加', ['class' => 'btn btn-primary']);
                ?>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="box">
            <div class="box-body">
                <?php
                echo FileUploadUI::widget([
                    'model' => $model,
                    'attribute' => 'upload_file',
                    'url' => $fileUploadUrl,//'http://localhost:8054/admin/demo/duandian', // your url, this is just for demo purposes,
                    'clientOptions' => [
                        'previewMaxWidth' => 300,
                        'previewMaxHeight' => 300,
                        'maxChunkSize' => 10000000
                    ],
                    // 'clientEvents' => [
                    //     'fileuploaddone' => $fileUploaddOneCallBack,
                    //     'fileuploadfail' => 'function(e, data) {
                    //                             // console.log(e);
                    //                             console.log(data);
                    //                         }',
                    // ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end();?>
