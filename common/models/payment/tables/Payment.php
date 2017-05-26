<?php
namespace common\models\payment\tables;

use Yii;
use common\base\ActiveRecord;

/**
 *
 */
class Payment extends ActiveRecord
{
    CONST TYPE_CNY = 'CNY';

    CONST ALIPAY = 'alipay';
    CONST WXPAY = 'wxpay';

    const USE_FOR_PC = 1;
    CONST USE_FOR_MOBILE = 2;

    const PS_PAYED = 2;
    CONST PS_NOT_PAY = 1;
    CONST PS_PAY_CANCEL = 3;

    CONST PES_NOT_ERROR = 1;
    CONST PES_ERROR_HAPPEN = 2;

    static private $_constMap = [];

    public static function getValidConsts($type, $onlyValue = false){
        if(empty(self::$_constMap)){
            self::$_constMap = [
                'p_type' => [
                    self::TYPE_CNY => Yii::t('app', '人名币')
                ],
                'p_pay_type' => [
                    self::ALIPAY => Yii::t('app', '支付宝'),
                    self::WXPAY => Yii::t('app', '微信支付')
                ],
                'po_info_type' => [
                    self::USE_FOR_PC => Yii::t('app', 'PC端使用场景'),
                    self::USE_FOR_MOBILE => Yii::t('app', '移动端使用场景')
                ],
                'po_pay_status' => [
                    self::PS_PAYED => Yii::t('app', '已经支付'),
                    self::PS_NOT_PAY => Yii::t('app', '未支付'),
                    self::PS_PAY_CANCEL => Yii::t('app', '支付取消')
                ],
                'po_error_status' => [
                    self::PES_NOT_ERROR => Yii::t('app', '错误状态'),
                    self::PES_ERROR_HAPPEN => Yii::t('app', '存在错误')
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
