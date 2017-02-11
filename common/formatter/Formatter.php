<?php
namespace common\formatter;

use yii\base\Component;
use yii\i18n\Formatter as BaseFormatter;
use yii\helpers\Json;
use yii\helpers\VarDumper;

/**
 *
 */
class Formatter extends BaseFormatter
{
    public function asJson($value){
        $data = Json::decode($value, true);
        if(!$data){
            return $value;
        }
        return VarDumper::dumpAsString($data, 100, true);
    }
}
