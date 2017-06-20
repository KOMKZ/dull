<?php
namespace common\models\log\tables;

use yii\db\ActiveRecord;
/**
 *
 */
class CronLog extends ActiveRecord
{
    public static function tableName(){
        return "{{%cron_log}}";
    }
}
