<?php
namespace common\formatter;

use yii\base\Component;
use yii\i18n\Formatter as BaseFormatter;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use branchonline\lightbox\Lightbox;

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
    public function asMap($value, $map){
        return array_key_exists($value, $map) ? $map[$value] : $value;
    }
    public function asImageThumb($value, $name = ''){
        return Lightbox::widget([
            'files' => [
                [
                    'thumb' => $value,
                    'thumbOptions' => [
                        'style' => "width:100px;",
                        'class' => 'img-response'
                    ],
                    'original' => $value,
                    'title' => $name,
                ],
            ]
        ]);
    }
}
