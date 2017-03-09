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
            <div class="form-group">
                <label for="">{{label}}</label>
                <input type="hidden" name="{{set_name}}[set_name]" value="{{set_name}}">
                <div class="form-inline">
                    {{region}}
                </div>
            </div>
HTML;
        if(is_string($item['value']) && preg_match('/[\w_\-]+,[\w_\-]+/', $item['value'])){
            $value = explode(',', $item['value']);
        }elseif(!is_array($item['value'])){
            $value = [0 => -1, 1 => -1, 2 => -1];
        }

        $w = Yii::createObject([
            'class' => RegionWidget::className(),
            'url'=> Yii::$app->apiurl->createAbsoluteUrl(['open/get-region']),
            'province'=>[
                'name' => $item['name'] . '[set_value][]',
                'value' => $value[0],
                'options'=>[ 'class'=>'form-control','prompt'=>'选择省份' ]
            ],
            'city'=>[
                'name' => $item['name'] . '[set_value][]',
                'value' => $value[1],
                'options'=>[ 'class'=>'form-control','prompt'=>'选择城市' ]
            ],
            'district'=>[
                'name' => $item['name'] . '[set_value][]',
                'value' => $value[2],
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
