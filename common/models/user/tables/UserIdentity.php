<?php
namespace common\models\user\tables;

use Yii;
use yii\db\ActiveRecord;
use common\models\user\tables\UserGroup;

/**
 *
 */
class UserIdentity extends ActiveRecord
{
    public static function tableName(){
        return "{{%user_identity}}";
    }
    public function getGroup_info(){
        return $this->hasOne(UserGroup::className(), ['ug_name' => 'ui_g_name'])->one();
    }

    public function getIsSuperRoot(){
        return UserGroup::ROOT_GROUP == $this->ui_g_name;
    }

    public function attributeLabels(){
        return [
            'ui_id' => Yii::t('app','主键'),
            'ui_uid' => Yii::t('app','用户id'),
            'ui_g_name' => Yii::t('app','用户组别'),
            'group_info.ug_description' => Yii::t('app','用户组别')
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
