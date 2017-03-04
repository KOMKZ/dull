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
        <form class="" action="" method="post">
            <div class="form-group">
                <label for="">{{label}}</label>
                <input type="hidden" name="set_name" value="{{set_name}}">
                <div class="row">
                    <div class="col-lg-6">
                        <input class="form-control" type="text" name="set_value" value="{{set_value}}">
                    </div>
                </div>
            </div>
        </form>
HTML;
        return strtr($html, [
            '{{label}}' => $item['label'],
            '{{set_name}}' => $item['name'],
            '{{set_value}}' => $item['value']
        ]);
    }
}
