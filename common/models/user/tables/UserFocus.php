<?php
namespace common\models\user\tables;

use yii\db\ActiveRecord;

/**
 *
 */
class UserFocus extends ActiveRecord{
    public static function tableName(){
        return "{{%user_focus}}";
    }
    public function getUser_info(){
        return $this->hasOne(User::className(), ['u_id' => 'uf_uid']);
    }
}
