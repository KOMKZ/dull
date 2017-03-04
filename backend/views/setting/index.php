<?php
use common\models\setting\SettingWidget;
?>

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <?php
                    $webTitle = $settings['web_title'];
                ?>
                <?php
                echo SettingWidget::render($webTitle);
                ?>
            </div>
        </div>
    </div>
</div>
