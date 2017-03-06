<?php
namespace common\models\setting\widgets;

use Yii;
use yii\base\Object;
use common\assets\DateTimePickerAsset;


/**
 *
 */
class Datetime extends Object
{
    public static function render($item){
        $id = '#input_' . $item['name'];
        $js =<<<JS
        $('{$id}').DateTimePicker({
            dateTimeFormat: "yyyy-MM-dd HH:mm:ss"
        });
JS;
        DateTimePickerAsset::register(YIi::$app->getView());
        Yii::$app->view->registerJs($js);

        $html = <<<HTML
        <div id="input_{{set_name}}"></div>
        <form class="" action="" method="post">
            <div class="form-group">
                <label for="">{{label}}</label>
                <input type="hidden" name="set_name" value="{{set_name}}">
                <input data-field="datetime" class="form-control" type="text" name="set_value" value="{{set_value}}">
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
