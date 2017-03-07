<?php
namespace common\models\open;

use common\base\Model;
use common\models\open\tables\Region;
use yii\helpers\ArrayHelper;

/**
 *
 */
class OpenModel extends Model
{
    public static function getRegion($parentId = 0 ){
        $result = Region::find()->where(['parent_id'=>$parentId])->asArray()->all();
        return ArrayHelper::map($result, 'id', 'name');
    }
}
