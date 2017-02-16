<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<?php $form = ActiveForm::begin();?>
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
    <div class="col-lg-6">
        <div class="box">
            <div class="box-header with-border">
                <h5>基础信息</h5>
            </div>
            <div class="box-body">
                <?php
                echo $form->field($baseModel, 'u_username')->textInput(['autofocus' => true]);
                echo $form->field($baseModel, 'u_email');
                echo $form->field($baseModel, 'u_status')->dropDownList($userStatusMap);
                echo $form->field($baseModel, 'u_auth_status')->dropDownList($userAuthStatusMap);
                echo $form->field($baseModel, 'password')->passwordInput();
                echo $form->field($baseModel, 'password_confirm')->passwordInput();
                ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="box">
            <div class="box-header with-border">
                <h5>身份信息</h5>
            </div>
            <div class="box-body">
                <?php
                echo $form->field($identityModel, 'ui_g_name')->dropDownList($userGroupMap);
                ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end();?>
