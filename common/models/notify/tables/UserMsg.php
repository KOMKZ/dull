<?php
namespace common\models\notify\tables;

use Yii;
use common\base\ActiveRecord;

/**
 *
 */
class UserMsg extends ActiveRecord
{
    static protected $_constMap;
    public static function tableName(){
        return "{{%user_msg}}";
    }
    public function attributeLabels(){
        return [
            'um_id' => '消息id',
            'um_uid' => '属于哪个用户',
            'um_mid' => '消息主id',
            'um_read_status' => '阅读状态',
            'um_title' => '消息标题',
            'um_content' => '消息内容',
            'um_created_at' => '创建时间'
        ];
    }
    public static function getValidConsts($type, $onlyValue = false){
        if(empty(self::$_constMap)){
            self::$_constMap = [
                'um_read_status' => [
                    '1' => Yii::t('app', '已经查看'),
                    '0' => Yii::t('app', '未查看'),
                ]
            ];
        }
        if(array_key_exists($type, self::$_constMap) && !empty(self::$_constMap[$type])){
            return $onlyValue ? array_keys(self::$_constMap[$type]) : self::$_constMap[$type];
        }else{
            throw new \Exception("zh:不存在常量映射定义{$type}");
        }
    }
}
