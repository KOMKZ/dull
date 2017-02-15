<?php
namespace common\models\rbac\tables;

use Yii;
use yii\db\ActiveRecord;

/**
 *
 */
class AuthItem extends ActiveRecord
{
    static private $_constMap = [];
    CONST TYPE_PERM = 2;
    CONST TYPE_ROLE = 1;

    public static function getValidConsts($type, $onlyValue = false){
        if(empty(self::$_constMap)){
            self::$_constMap = [
                'type' => [
                    self::TYPE_PERM => Yii::t('app','权限'),
                    self::TYPE_ROLE => Yii::t('app', '角色')
                ]
            ];
        }
        if(array_key_exists($type, self::$_constMap) && !empty(self::$_constMap[$type])){
            return $onlyValue ? array_keys(self::$_constMap[$type]) : self::$_constMap[$type];
        }else{
            throw new \Exception("zh:不存在常量映射定义{$type}");
        }
    }

    public static function tableName(){
        return "{{%auth_item}}";
    }

    public function attributeLabels(){
        return [
            'name' => '名称',
            'type' => '类型',
            'description' => '描述',
            'rule_name' => '规则名称',
            'data' => '相关数据',
            'created_at' => '创建时间',
            'updated_at' => '更新时间'
        ];
    }
}
