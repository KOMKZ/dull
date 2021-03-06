<?php
use common\assets\ICheckAsset;
use yii\bootstrap\ActiveForm;
ICheckAsset::register($this);
$script = <<<EOT
$('input').iCheck({
  checkboxClass: 'icheckbox_square-blue',
  radioClass: 'iradio_square-blue',
  increaseArea: '20%' // optional
});
EOT;
$this->registerJs($script);
?>
<div class="login-box">
    <div class="login-logo">
        <a href=""><b>TRSTPS</b>LTE</a>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>
        <form action="#" method="post">
            <div class="form-group has-feedback">
                <input autofocus name="login_id" type="text" class="form-control" placeholder="username">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input name="password" type="password" class="form-control" placeholder="Password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input name="remember" value=1 type="checkbox"> Remember Me
                        </label>
                    </div>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
            </div>
        </form>
        <a href="#">I forgot my password</a><br>

    </div>
</div>
