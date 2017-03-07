<?php
use Yii;
use common\models\setting\SettingWidget;
use common\models\open\OpenModel;
?>

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-12">
                        <?php
                        $item = $settings['web_region'];
                        echo SettingWidget::render($item);
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
