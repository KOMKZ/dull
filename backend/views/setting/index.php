<?php
use Yii;
use common\models\setting\SettingWidget;
use common\models\open\OpenModel;
?>
<form class="" action="" method="post">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <?php
                        foreach($settings as $item){
                            echo '<div class="col-lg-12">';
                            echo SettingWidget::render($item);
                            echo '</div>';
                        }
                        ?>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <input type="submit" name="" value="提交" class="btn btn-default">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>
