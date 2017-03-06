<?php
use common\models\setting\SettingWidget;
?>

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-6">
                        <?php
                        $authorBirthday = $settings['web_01'];
                        echo SettingWidget::render($authorBirthday);

                        ?>
                    </div>
                    <div class="col-lg-6">
                        <?php
                        // $webTitle = $settings['web_title'];
                        // echo SettingWidget::render($webTitle);
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
