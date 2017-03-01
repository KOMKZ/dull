<?php
namespace common\models\user\tables;

use Yii;
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

    public function attributeLabels(){
        return [
            'u_username' => Yii::t('app', '用户名'),
            'u_f_username' => Yii::t('app', '用户名')
        ];
    }
}
