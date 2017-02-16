<?php
namespace common\models\user\tables;

use yii\db\ActiveRecord;

/**
 *
 */
class UserIdentity extends ActiveRecord
{
    public static function tableName(){
        return "{{%user_identity}}";
    }
    public function attributeLabels(){
        return [
            'ui_id' => '主键',
            'ui_uid' => '用户id',
            'ui_g_name' => '用户组别'
        ];
    }

    public function rules(){
        return [
            ['ui_g_name', 'required'],
            ['ui_g_name', 'exist', 'targetClass' => UserGroup::className(), 'targetAttribute' => 'ug_name']
        ];
    }

    public function scenarios(){
        return [
            'create' => [
                'ui_g_name'
            ]
        ];
    }
}
