<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<div class="row">
    <div class="col-lg-5">
        <div class="box">
            <div class="box-body">
                <?php
                $form = ActiveForm::begin();
                echo $form->field($model, 'u_username')->textInput([
                    'disabled' => true,
                ]);
                echo $form->field($model, 'u_email')->textInput([
                    'disabled' => true
                ]);
                echo $form->field($model, 'u_status')->dropDownList($userStatusMap);
                echo $form->field($model, 'u_auth_status')->dropDownList($userAuthStatusMap);
                echo $form->field($model, 'password')->passwordInput();
                echo $form->field($model, 'password_confirm')->passwordInput();
                echo $form->field($model, 'u_created_at_format')->textInput([
                    'disabled' => true
                ]);
                echo $form->field($model, 'u_updated_at_format')->textInput([
                    'disabled' => true
                ]);
                echo Html::submitButton('添加', ['class' => 'btn btn-primary']);
                ActiveForm::end();
                ?>
            </div>
        </div>
    </div>
</div>
