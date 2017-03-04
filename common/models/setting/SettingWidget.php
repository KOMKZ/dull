<?php
namespace common\models\setting;

use yii\base\Object;

/**
 *
 */
class SettingWidget extends Object
{
    const W_TEXT = 1;

    public static function render($setting){
        $renderClass = self::getRenderClass($setting['set_widget']);
        return $renderClass::render(self::buildRenderData($setting));
    }

    protected static function buildRenderData($item){
        return [
            'label' => $item['set_des'],
            'name' => $item['set_name'],
            'value' => $item['set_value'],
            'input_options' => $item['set_widget_params']
        ];
    }

    public static function getRenderClass($type){
        switch ($type) {
            case self::W_TEXT:
                return '\common\models\setting\widgets\Text';
            default:
                throw new \Exception('Unsupport setting render widget definations');
        }
    }



}
