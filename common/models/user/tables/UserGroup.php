<?php
namespace common\models\user\tables;

use yii\db\ActiveRecord;

/**
 *
 */
class UserGroup extends ActiveRecord
{
    CONST ROOT_GROUP = 1;
    CONST ADMIN_GROUP = 2;
    CONST TEST_GROUP = 3;
    CONST VISTOR_GROUP = 4;

    public static function tableName(){
        return "{{%user_group}}";
    }
    public function attributeLabels(){
        return [
            'ug_id' => '用户组id',
            'ug_name' => '用户组名称',
            'ug_description' => '用户组说明',
            'ug_created_at' => '创建时间',
            'ug_updated_at' => '更新时间'
        ];
    }
}
