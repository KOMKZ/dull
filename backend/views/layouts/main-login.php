<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use dmstr\widgets\Alert;

/* @var $this \yii\web\View */
/* @var $content string */

dmstr\web\AdminLteAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="login-page">

<?php $this->beginBody() ?>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div id="alert">
                <?= Alert::widget() ?>
            </div>
        </div>
    </div>
    <?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
