<?php
namespace common\models\order\tables;

use Yii;
use common\base\ActiveRecord;
use common\models\order\TransModel;
use common\models\payment\tables\Payment;
use common\models\payment\PayModel;

/**
 *
 */
class Trans extends ActiveRecord
{
    CONST TYPE_CONSUME = 1;// 消费交易
    CONST TYPE_REFUND = 2;// 退款交易

    CONST TS_INIT = 1;
    CONST TS_CANCEL = 2;
    CONST TS_COMPLETED = 3;

    CONST TPS_NOT_PAY = 1;
    CONST TPS_PAYED = 2;

    CONST TES_NO_ERROR = 1;
    CONST TES_ERROR_HAPPEND = 2;

    static protected $_constMap = [];


    public static function tableName(){
        return "{{%trans}}";
    }

    public function behaviors(){
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 't_created_at',
                'updatedAtAttribute' => 't_updated_at'
            ]
        ];
    }

    public function rules(){
        return [
            ['t_title', 'required'],
            ['t_title', 'string', 'min' => 5, 'max' => 200],

            ['t_type', 'required'],
            ['t_type', 'in', 'range' => self::getValidConsts('t_type', true)],

            ['t_fee_type', 'default', 'value' => Payment::TYPE_CNY],
            ['t_fee_type', 'in', 'range' => Payment::getValidConsts('p_type', true)],
            ['t_error_status', 'default', 'value' => self::TES_NO_ERROR],
            ['t_error_status', 'in', 'range' => self::getValidConsts('t_error_status', true)],

            ['t_fee', 'required'],
            ['t_fee', 'checkPriceType'],

            ['t_pa_uid', 'required'],
            ['t_pb_uid', 'required'],

            ['t_out_trade_no', 'required'],
            ['t_out_trade_no', 'unique'],
            ['t_out_trade_type', 'integer'],
            ['t_app_id', 'integer'],

            ['t_des', 'string'],
            ['t_des', 'default', 'value' => '']
        ];
    }

    public function checkPriceType($attr){
        if(!PayModel::checkPriceType($this->$attr)){
            $this->addError($attr, '金额数值不合法，必须是整数，同时在必须在范围之间');
        }
    }

    public function scenarios(){
        return [
            'create' => [
                't_title',
                't_type',
                't_fee_type',
                't_fee',
                't_pa_uid',
                't_pb_uid',
                't_des',
                't_out_trade_no',
                't_out_trade_type',
                't_app_id',
                't_error_status'
            ]
        ];
    }
    public static function getValidConsts($type = null, $onlyValue = false){
        if(empty(self::$_constMap)){
            self::$_constMap = [
                't_type' => [
                    self::TYPE_CONSUME => Yii::t('app', '消费交易'),
                    self::TYPE_REFUND => Yii::t('app', '退款交易'),
                ],
                't_status' => [
                    self::TS_INIT => Yii::t('app', '初始化'),
                    self::TS_CANCEL => Yii::t('app', '已经取消'),
                    self::TS_COMPLETED => Yii::t('app', '已经完成')
                ],
                't_pay_status' => [
                    self::TPS_NOT_PAY => Yii::t('app', '未支付'),
                    self::TPS_PAYED => Yii::t('app', '已经支付')
                ],
                't_error_status' => [
                    self::TES_NO_ERROR => Yii::t('app', '没有错误'),
                    self::TES_ERROR_HAPPEND => Yii::t('app', '发生过错误')
                ]
            ];
        }
        if(array_key_exists($type, self::$_constMap) && !empty(self::$_constMap[$type])){
            return $onlyValue ? array_keys(self::$_constMap[$type]) : self::$_constMap[$type];
        }elseif(null == $type){
            return self::$_constMap;
        }else{
            throw new \Exception("zh:不存在常量映射定义{$type}");
        }
    }
    public function attributeLabels(){
        return [
            't_id' => Yii::t('app', 't_id'),
            't_title' => Yii::t('app', '交易标题'),
            't_number' => Yii::t('app', '交易编号'),
            't_type' => Yii::t('app', '交易类型'),
            't_fee_type' => Yii::t('app', '交易货币类型'),
            't_fee' => Yii::t('app', '交易金额'),
            't_created_at' => Yii::t('app', '创建时间'),
            't_updated_at' => Yii::t('app', '更新时间'),
            't_closed_time' => Yii::t('app', '关闭试驾'),
            't_payed_time' => Yii::t('app', '支付时间'),
            't_pa_uid' => Yii::t('app', '付款人'),
            't_pb_uid' => Yii::t('app', '收款人'),
            't_des' => Yii::t('app', '交易的描述'),
            't_out_trade_no' => Yii::t('app', '交易应用号'),
            't_out_trade_type' => Yii::t('app', '交易应用类型'),
            't_app_id' => Yii::t('app', '交易应哟给你id'),
            't_status' => Yii::t('app', '交易的状态'),
            't_pay_status' => Yii::t('app', '交易的支付状态'),
            't_error_status' => Yii::t('app', '交易的错误状态'),
            't_succ_pay_type' => Yii::t('app', '最终成功的支付方式'),
            't_invalid_after' => Yii::t('app', "交易失效时间")
        ];
    }

}
