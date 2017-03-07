<?php
namespace common\models\setting\tables;

use Yii;
use common\base\ActiveRecord;
use common\models\setting\SettingWidget;


/**
 *
 */
class Setting extends ActiveRecord
{
    CONST M_WEBSITE = 1;

    const STRING = 1;
    const DATETIME = 2;
    const NUMBER = 3;


    const W_TEXT = 1;

    static protected $_constMap = [];

    public static function tableName(){
        return "{{%setting}}";
    }

    public function behaviors(){
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'set_created_at',
                'updatedAtAttribute' => false
            ]
        ];
    }

    public function rules(){
        return [
            ['set_name', 'required'],
            ['set_name', 'string', 'min' => 2, 'max' => 30],

            ['set_value_type', 'default', 'value' => self::STRING],
            ['set_value_type', 'in', 'range' => self::getValidConsts('set_value_type', true)],

            ['set_des', 'string', 'max' => 100],

            ['set_parent_id', 'default', 'value' => 0],
            ['set_parent_id', 'number'],

            ['set_module', 'default', 'value' => 0],
            ['set_module', 'number'],

            ['set_active', 'default', 'value' => 1],
            ['set_active', 'filter', 'filter' => 'intval'],

            [
                [
                    'set_value',
                    // 'set_validators',
                    // 'set_validators_params',
                    'set_widget',
                    'set_widget_params',

                    // extra_fileds
                    'set_module_name'
                ], 'safe'
            ]
        ];
    }

    public static function getValidConsts($type, $onlyValue = false){
        if(empty(self::$_constMap)){
            self::$_constMap = [
                'set_value_type' => [
                    self::STRING => Yii::t('app', '字符串'),
                    self::DATETIME => Yii::t('app', '时间戳'),
                    self::NUMBER => Yii::t('app', '数字')
                ],
                'set_module' => [
                    self::M_WEBSITE => Yii::t('app', '网站设置'),
                ],
                'set_widget' => [
                    SettingWidget::W_TEXT => Yii::t('app', '普通文本框'),
                    SettingWidget::W_DATETIME => Yii::t('app', '日期时间框'),
                    SettingWidget::W_DROPDOWN => Yii::t('app', '下拉单选框'),
                    SettingWidget::W_CHECKBOX => Yii::t('app', 'checkbox多选框'),
                    SettingWidget::W_RADIOLIST => Yii::t('app', 'radiolist单选框')
                ],
            ];
        }
        if(array_key_exists($type, self::$_constMap) && !empty(self::$_constMap[$type])){
            return $onlyValue ? array_keys(self::$_constMap[$type]) : self::$_constMap[$type];
        }else{
            throw new \Exception("zh:不存在常量映射定义{$type}");
        }
    }

    public function fields(){
        $fields = parent::fields();
        return array_merge($fields,[
            'set_module_name',
            'set_value_type_name',
            'set_widget_name'
        ]);
    }



    public function getSet_value_type_name(){
        $map = self::getValidConsts('set_value_type');
        return $map[$this->set_value_type];
    }

    public function getSet_module_name(){
        $map = self::getValidConsts('set_module');
        return $map[$this->set_module];
    }

    public function getSet_widget_name(){
        $map = self::getValidConsts('set_widget');
        return $map[$this->set_widget];
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
