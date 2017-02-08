<?php
namespace common\models\log\tables;

use yii\db\ActiveRecord;

/**
 *
 */
class LogBackend extends ActiveRecord
{
    public static function tableName(){
        return "log_backend";
    }
}
