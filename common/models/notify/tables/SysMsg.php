<?php
namespace common\models\notify\tables;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\base\ActiveRecord;
use common\models\notify\NotifyModel;
use common\models\user\tables\User;


/**
 *
 */
class SysMsg extends ActiveRecord
{
    const GLOBAL_MSG = 1;
    const PRIVATE_MSG = 2;
    const FOCUS_PULL_MSG = 3;



    static protected $_constMap = null;

    public function getIsUseTpl(){
        return 1 == $this->sm_use_tpl;
    }

    public function getIsGlobalMsg(){
        return self::GLOBAL_MSG == $this->sm_object_type;
    }

    public function getIsPrivateMsg(){
        return self::PRIVATE_MSG == $this->sm_object_type;
    }


    public function getCreate_user(){
        return $this->hasOne(User::className(), ['u_id' => 'sm_create_uid']);
    }




    public static function tableName(){
        return "{{%sys_msg}}";
    }

    public function attributeLabels(){
        return [
            'sm_id' => '消息',
            'sm_title' => '消息标题',
            'sm_content' => '消息内容',
            'sm_create_uid' => '发送人用户id',
            'create_user.u_username' => '发送者用户名',
            'sm_object_type' => '接受对象类型',
            'sm_object_id' => '接受对象id',
            'sm_expired_at' => '过期时间',
            'sm_created_at' => '创建时间',
            'sm_tpl_type' => '模板的类型',
            'sm_use_tpl' => '是否使用消息模板'
        ];
    }
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'sm_created_at',
                'updatedAtAttribute' => false
            ]
        ];
    }
    public function rules(){
        return [
            ['sm_expired_at', 'default', 'value' => time() + 3600*24],

            ['sm_object_type', 'required'],
            ['sm_object_type', 'in', 'range' => self::getValidConsts('sm_object_type', true)],

            ['sm_tpl_type', 'in', 'range' => self::getValidConsts('sm_tpl_type', true)],

            ['sm_use_tpl', 'filter', 'filter' => 'intval'],
            ['sm_object_id', 'filter', 'filter' => 'trim']
        ];
    }
    public function scenarios(){
        return [
            'create' => [
                'sm_title',
                'sm_content',
                'sm_object_type',
                'sm_object_id',
                'sm_expired_at',
                'sm_tpl_type',
                'sm_use_tpl',
            ]
        ];
    }

    public static function getValidConsts($type, $onlyValue = false){
        if(empty(self::$_constMap)){
            self::$_constMap = [
                'sm_use_tpl' => [
                    '1' => Yii::t('app', '使用模板'),
                    '0' => Yii::t('app', '不使用模板'),
                ],
                'sm_object_type' => [
                    self::GLOBAL_MSG => Yii::t('app', '全局消息'),
                    self::PRIVATE_MSG => Yii::t('app', '个人消息'),
                    self::FOCUS_PULL_MSG => Yii::t('app', '关注用户拉取消息')
                ],
                'sm_tpl_type' => NotifyModel::getMTplTypeMap(true)
            ];
        }
        if(array_key_exists($type, self::$_constMap) && !empty(self::$_constMap[$type])){
            return $onlyValue ? array_keys(self::$_constMap[$type]) : self::$_constMap[$type];
        }else{
            throw new \Exception("zh:不存在常量映射定义{$type}");
        }
    }
}
