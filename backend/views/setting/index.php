<?php
use Yii;
use common\models\setting\SettingWidget;
use common\models\open\OpenModel;
$js = <<<JS
    $('#form').submit(function(){
        $.post($(this).attr('action'), $(this).serialize(), function(res){
            if(res.code > 0){
                return dull.new_alert(res.code + ':' + res.message, 'error');
            }else{
                return dull.new_alert('修改成功', 'success');
            }
        }, 'json')
        return false;
    });
JS;
$this->registerJs($js);
?>
<form id="form" class="" action="<?= $url['setting/update-all']?>" method="post">
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
