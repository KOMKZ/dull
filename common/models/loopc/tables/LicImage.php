<?php
namespace common\models\loopc\tables;

use Yii;
use common\base\ActiveRecord;


class LicImage extends ActiveRecord{
    public static function tableName(){
        return "{{%lic_image}}";
    }
}
