<?php
use Yii;
use common\models\setting\SettingWidget;
use common\models\open\OpenModel;
$url = Yii::$app->apiurl->createAbsoluteUrl(['open/get-region']);
?>

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <!-- <div class="col-lg-6">
                        <?php
                        // $item = $settings['web_01'];
                        // echo SettingWidget::render($item);
                        ?>
                    </div> -->
                    <div class="col-lg-6">
                        <form class="form-inline" action="index.html" method="post">
                                <?php
                                $w = Yii::createObject([
                                    'class' => \common\widgets\Region::className(),
                                    'url'=> $url,
                                    'name' => 'set_value[]',
                                    'province'=>[
                                        'value' => 1,
                                        'options'=>[ 'class'=>'form-control','prompt'=>'选择省份' ]
                                    ],
                                    'city'=>[
                                        'value' => 2801,
                                        'options'=>[ 'class'=>'form-control','prompt'=>'选择城市' ]
                                    ],
                                    'district'=>[
                                        'value' => null,
                                        'options'=>[ 'class'=>'form-control','prompt'=>'选择县/区']
                                    ]
                                ]);
                                echo $w->run();
                                ?>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
