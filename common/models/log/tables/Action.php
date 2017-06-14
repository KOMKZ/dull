<?php
namespace common\models\log\tables;

use yii\db\ActiveRecord;
/**
 *
 */
class Action extends ActiveRecord
{
    public static function tableName(){
        return "{{%action_log}}";
    }
    // public function attributeLabels(){
    //     return [
    //         'al_id' => '主键',
    //         'al_module' => '模块id',
    //         'al_action' => '动作名称',
    //         'al_uid' => '用户id',
    //         'al_object_id' => '对象id',
    //         'al_app_id' => 'APPid',
    //         'al_ip' => 'IP',
    //         'al_agent_info' => '代理信息',
    //         'al_created_time' => '创建时间'
    //     ];
    // }
}
