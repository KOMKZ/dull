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
    static protected $hasRegister = false;
    public static function registerJs(){
        if(self::$hasRegister){
            return false;
        }
        $js = <<<JS
        $('.setting-icheck-checkbox').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
JS;
        ICheckAsset::register(Yii::$app->view);
        Yii::$app->view->registerJs($js);
    }
    public static function render($item){
        self::registerJs();
        $html = <<<HTML
        <form class="" action="" method="post">
            <div class="form-group">
                <label for="">{{label}}</label>
                <input type="hidden" name="set_name" value="{{set_name}}">
                {{checkbox}}
            </div>
        </form>
HTML;
        $params = json_decode($item['params'], true);
        return strtr($html, [
            '{{label}}' => $item['label'],
            '{{set_name}}' => $item['name'],
            '{{set_value}}' => $item['value'],
            '{{checkbox}}' => Html::checkboxList('set_value', $item['value'], $params['map'], [
                'class' => 'checkbox setting-icheck-checkbox'
            ])
        ]);
    }
}
