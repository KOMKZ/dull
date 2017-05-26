<?php
namespace common\models\payment\tables;

use Yii;
use common\base\ActiveRecord;
use common\models\payment\tables\Payment;

/**
 *
 */
class PayOrder extends ActiveRecord
{
    public static function tableName(){
        return "{{%pay_order}}";
    }
    public function behaviors(){
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'po_created_at',
                'updatedAtAttribute' => 'po_updated_at'
            ]
        ];
    }
    public function rules(){
        return [
            ['po_tid', 'required', 'skipOnEmpty' => true],

            ['po_type', 'required'],
            ['po_type', 'in', 'range' => Payment::getValidConsts('p_pay_type', true)],

            ['po_pay_status', 'in', 'range' => Payment::getValidConsts('po_pay_status', true)],
            ['po_error_status', 'in', 'range' => Payment::getValidConsts('po_error_status', true)],

            ['po_info_type', 'default', 'value' => Payment::USE_FOR_PC],
            ['po_info_type', 'in', 'range' => Payment::getValidConsts('po_info_type', true)]

        ];
    }
    public function scenarios(){
        return [
            'create' => [
                'po_tid',
                'po_type',
                'po_info_type',
                'po_invalid_after'
            ]
        ];
    }
    public function attributeLabels(){
        return [
            'po_id' => Yii::t('app', 'po_id'),
            'po_tid' => Yii::t('app', '交易的id'),
            'po_type' => Yii::t('app', '支付类型'),
            'po_pay_status' => Yii::t('app', '支付的状态'),
            'po_error_status' => Yii::t('app', '支付的错误状态'),
            'po_third_data' => Yii::t('app', '支付单的原始数据'),
            'po_info_type' => Yii::t('app', '支付单的信息类型'),
            'po_created_at' => Yii::t('app', '创建时间'),
            'po_updated_at' => Yii::t('app', '更新时间'),
            'po_invalid_after' => Yii::t('app', '支付单的失效时间')
        ];
    }


}
