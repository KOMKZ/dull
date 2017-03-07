<?php
namespace common\models\setting\widgets;

use Yii;
use common\widgets\Region as RegionWidget;
use yii\base\Object;

/**
 *
 */
class Region extends Object
{
    public static function render($item){
        $html = <<<HTML
        <form action="" method="post">
            <div class="form-group">
                <label for="">{{label}}</label>
                <input type="hidden" name="set_name" value="{{set_name}}">
                <div class="form-inline">
                    {{region}}
                </div>
            </div>
        </form>
HTML;
        $w = Yii::createObject([
            'class' => RegionWidget::className(),
            'url'=> Yii::$app->apiurl->createAbsoluteUrl(['open/get-region']),
            'province'=>[
                'name' => 'set_value[]',
                'value' => 1,
                'options'=>[ 'class'=>'form-control','prompt'=>'选择省份' ]
            ],
            'city'=>[
                'name' => 'set_value[]',
                'value' => 2801,
                'options'=>[ 'class'=>'form-control','prompt'=>'选择城市' ]
            ],
            'district'=>[
                'name' => 'set_value[]',
                'value' => 2827,
                'options'=>[ 'class'=>'form-control','prompt'=>'选择县/区']
            ]
        ]);
        return strtr($html, [
            '{{label}}' => $item['label'],
            '{{set_name}}' => $item['name'],
            '{{region}}' => $w->run()

        ]);
    }
}
