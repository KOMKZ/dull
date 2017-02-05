<?php
namespace common\models\email\tables;

use yii\db\ActiveRecord;

/**
 *
 */
class EmailFailed extends ActiveRecord
{
    public static function tableName(){
        return "{{%email_failed}}";
    }
}
