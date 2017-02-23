<?php
namespace common\models\notify\tables;

use common\base\ActiveRecord;

/**
 *
 */
class Msg extends ActiveRecord
{
    public static function tableName(){
        return "{{%msg}}";
    }
    public function attributeLabels(){
        return [
            'm_id' => '消息id',
            'm_title' => '消息标题',
            'm_content' => '消息内容',
            'm_params_map' => '消息参数',
            'm_created_at' => '创建时间'
        ];
    }
    public function scenarios(){
        return [
            'create' => [
                'm_title'
            ],
        ];
    }
}
