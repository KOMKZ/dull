<?php
namespace common\models\notify;

use common\base\Model;
use yii\helpers\ArrayHelper;

/**
 *
 */
class NotifyModel extends Model
{
    public static function getMTplTypeMap($noContent = false){
        $map = require(dirname(__FILE__) . '/' . 'm-tpl-type-map.php');
        if(!$noContent){
            return $map;
        }else{
            return ArrayHelper::map($map, 'value', 'label');
        }

    }
    public function createSysMsg($data){

    }
}
