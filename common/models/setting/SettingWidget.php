<?php
namespace common\models\setting;

use yii\base\Object;

/**
 *
 */
class SettingWidget extends Object
{
    const W_TEXT = 1;
    const W_DATETIME = 2;
    const W_DROPDOWN = 3;
    const W_CHECKBOX = 4;
    const W_RADIOLIST = 5;
    const W_REGION = 6;

    public static function render($setting){
        $renderClass = self::getRenderClass($setting['set_widget']);
        return $renderClass::render(self::buildRenderData($setting));
    }

    protected static function buildRenderData($item){
        return [
            'label' => $item['set_des'],
            'name' => $item['set_name'],
            'value' => $item['set_value'],
            'params' => $item['set_widget_params']
        ];
    }

    public static function getRenderClass($type){
        switch ($type) {
            case self::W_TEXT:
                return '\common\models\setting\widgets\Text';
            case self::W_DATETIME:
                return '\common\models\setting\widgets\Datetime';
            case self::W_DROPDOWN:
                return '\common\models\setting\widgets\Dropdown';
            case self::W_CHECKBOX:
                return '\common\models\setting\widgets\CheckBox';
            case self::W_RADIOLIST:
                return '\common\models\setting\widgets\RadioList';
            case self::W_REGION:
                return '\common\models\setting\widgets\Region';
            default:
                throw new \Exception('Unsupport setting render widget definations');
        }
    }



}
