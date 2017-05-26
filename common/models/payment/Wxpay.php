<?php
namespace common\models\payment;

use Yii;
use yii\base\Object;
use common\models\payment\tables\Payment;

/**
 *
 */
class Wxpay extends Object
{
    protected function wxpay(){
        return Yii::$app->wxpay;
    }
    public function validateData($data){
        return $this->wxpay()->checkDataValid($data);
    }
    public function createOrder($data){
        $wxpay = $this->wxpay();
        $wxOrder = $wxpay->createOrder();
        $wxOrder->setBody($data['t_title']);
        $wxOrder->setProductId($data['t_out_trade_no']);
        $wxOrder->setOutTradeNo($data['t_out_trade_no']);
        $wxOrder->setTotalFee($data['t_fee']);
        $wxOrder->setTimeStart(date("YmdHis", time()));
        $wxOrder->setTimeExpire(date("YmdHis", time() + $data['t_pay_duration']));
        $wxOrder->setNotifyUrl($wxpay->notifyUrl);
        $wxOrderResult = $wxpay->sendOrder($wxOrder);
        if(!$wxOrderResult->isSuccess()){
            return [null, '创建微信预支付单失败:' . $wxOrderResult->getErrMsg()];
        }
        if(Payment::USE_FOR_MOBILE == $data['t_info_type']){
            $payData = $wxOrderResult->getValue('prepay_id');
        }else{
            $payData = $wxOrderResult->getValue('code_url');
        }
        return [$payData, null];
    }
}
