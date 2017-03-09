<?php
namespace common\models\setting\widgets;

use yii\base\Object;
use yii\helpers\Html;

/**
 *
 */
class Dropdown extends Object
{
    public static function render($item){
        $html = <<<HTML
            <div class="form-group">
                <label for="">{{label}}</label>
                <input type="hidden" name="{{set_name}}[set_name]" value="{{set_name}}">
                {{select_input}}
            </div>
HTML;
        $params = json_decode($item['params'], true);
        return strtr($html, [
            '{{label}}' => $item['label'],
            '{{set_name}}' => $item['name'],
            '{{set_value}}' => $item['value'],
            '{{select_input}}' => Html::DropdownList($item['name'] . '[set_value]', $item['value'], $params['map'], [
                'class' => 'form-control'
            ])
        ]);
    }
}
