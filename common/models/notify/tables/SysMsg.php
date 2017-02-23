<?php
namespace common\models\notify\tables;

use common\base\ActiveRecord;

/**
 *
 */
class SysMsg extends ActiveRecord
{
    public $m_title;
    public $tpl_type;


    public static function tableName(){
        return "{{%sys_msg}}";
    }
    public function attributeLabels(){
        return [
            'sm_id' => '消息',
            'm_title' => '消息标题',
            'sm_mid' => '消息主体id',
            'sm_create_uid' => '发送人用户id',
            'sm_object_type' => '接受对象类型',
            'sm_object_id' => '接受对象id',
            'sm_expired_at' => '过期时间',
            'sm_created_at' => '创建时间',
            'tpl_type' => '模板的类型'
        ];
    }
    public function scenarios(){
        return [

        ];
    }
}
