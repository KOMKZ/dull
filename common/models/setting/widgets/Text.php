<?php
namespace common\models\setting\widgets;

use yii\base\Object;

/**
 *
 */
class Text extends Object
{
    public static function render($item){
        $html = <<<HTML
            <div class="form-group">
                <label for="">{{label}}</label>
                <input type="hidden" name="{{set_name}}[set_name]" value="{{set_name}}">
                <input class="form-control" type="text" name="{{set_name}}[set_value]" value="{{set_value}}">
            </div>
HTML;
        return strtr($html, [
            '{{label}}' => $item['label'],
            '{{set_name}}' => $item['name'],
            '{{set_value}}' => $item['value']
        ]);
    }
}
