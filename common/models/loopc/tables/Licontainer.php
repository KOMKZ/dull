<?php
namespace common\models\loopc\tables;

use Yii;
use common\base\ActiveRecord;


class Licontainer extends ActiveRecord{
    public static function tableName(){
        return "{{%loop_img_container}}";
    }
}
