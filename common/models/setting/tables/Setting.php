<?php
namespace common\models\setting\tables;

use common\base\ActiveRecord;

/**
 *
 */
class Setting extends ActiveRecord
{
    public static function tableName(){
        return "{{%setting}}";
    }

    public function attributeLabels(){
        return [
            'set_id' => '主键id',
            'set_name' => '设置项的名称',
            'set_value' => '设置项值',
            'set_value_type' => '设置项的类型',
            'set_des' => '设置项的描述',
            'set_module' => '设置项所属模块',
            'set_parent_id' => '父类设置项的id',
            'set_validators' => '设置项验证器',
            'set_validators_params' => '设置项验证器参数',
            'set_widget' => '设置项组件',
            'set_widget_params' => '设置项组件参数',
            'set_active' => '设置项是否可用',
            'set_created_at' => '创建时间',
        ];
    }
}
