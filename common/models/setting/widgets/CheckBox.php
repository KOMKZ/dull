<?php
namespace common\models\setting\widgets;

use Yii;
use yii\base\Object;
use yii\helpers\Html;
use common\assets\ICheckAsset;




/**
 *
 */
class CheckBox extends Object
{
    public static function render($item){
        ICheckAsset::register(Yii::$app->view);
        
        $html = <<<HTML
        <form class="" action="" method="post">
            <div class="form-group">
                <label for="">{{label}}</label>
                <input type="hidden" name="set_name" value="{{set_name}}">
                {{select_input}}
            </div>
        </form>
HTML;
        $params = json_decode($item['params'], true);
        return strtr($html, [
            '{{label}}' => $item['label'],
            '{{set_name}}' => $item['name'],
            '{{set_value}}' => $item['value'],
            '{{select_input}}' => Html::checkboxList('set_value', $item['value'], $params['map'], [
                'class' => 'form-control'
            ])
        ]);
    }
}
