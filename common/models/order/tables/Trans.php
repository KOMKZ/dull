<?php
namespace common\models\order\tables;

use yii\db\ActiveRecord;

/**
 *
 */
class Trans extends ActiveRecord
{
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


    public function attributeLabels(){
        return [
            't_id' => Yii::t('app', 't_id'),
            't_bill_title' => Yii::t('app', 't_bill_title'),
            't_number' => Yii::t('app', 't_number'),
            't_type' => Yii::t('app', 't_type'),
            't_fee_type' => Yii::t('app', 't_fee_type'),
            't_fee' => Yii::t('app', 't_fee'),
            't_created_at' => Yii::t('app', 't_created_at'),
            't_updated_at' => Yii::t('app', 't_updated_at'),
            't_closed_time' => Yii::t('app', 't_closed_time'),
            't_payed_time' => Yii::t('app', 't_payed_time'),
            't_pa_uid' => Yii::t('app', 't_pa_uid'),
            't_pb_uid' => Yii::t('app', 't_pb_uid'),
            't_bill_des' => Yii::t('app', 't_bill_des'),
            't_out_trade_no' => Yii::t('app', 't_out_trade_no'),
            't_out_trade_type' => Yii::t('app', 't_out_trade_type'),
            't_app_id' => Yii::t('app', 't_app_id'),
            't_status' => Yii::t('app', 't_status'),
            't_pay_status' => Yii::t('app', 't_pay_status')
        ];
    }

}
