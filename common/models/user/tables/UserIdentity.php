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
        return $this->hasOne(UserGroup::className(), ['ug_id' => 'ui_gid'])->one();
    }

    public function getIsSuperRoot(){
        return UserGroup::ROOT_GROUP == $this->ui_gid;
    }

    public function attributeLabels(){
        return [
            'ui_id' => Yii::t('app','主键'),
            'ui_uid' => Yii::t('app','用户id'),
            'ui_gid' => Yii::t('app','用户组别id'),
            'group_info.ug_description' => Yii::t('app','用户组别id')
        ];
    }

    public function rules(){
        return [
            ['ui_gid', 'required'],
            ['ui_gid', 'exist', 'targetClass' => UserGroup::className(), 'targetAttribute' => 'ug_id']
        ];
    }

    public function scenarios(){
        return [
            'create' => [
                'ui_gid'
            ]
        ];
    }
}
