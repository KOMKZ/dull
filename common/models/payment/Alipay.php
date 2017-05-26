<?php
namespace common\models\payment;

use Yii;
use yii\base\Object;
use common\models\payment\tables\Payment;


/**
 *
 */
class Alipay extends Object
{
    protected function alipay(){
        return Yii::$app->alipay;
    }
    public function checkDataMatch($aliOrder, $transData){
        return $aliOrder->total_amount == ''.($transData['fee']/100);
    }
    public function getTradeNo($data){
        return $data['out_trade_no'];
    }
    public function validatePayed($data){
        $alipay = $this->alipay();
        $aliOrder = $alipay->queryOrder($data['out_trade_no']);
        return [$aliOrder && ('TRADE_SUCCESS' == $aliOrder->trade_status), $aliOrder];
    }
    public function validateData($data){
        $alipay = $this->alipay();
        return $alipay->getInstance()->rsaCheckV1($data, $alipay->alipayrsaPublicKey, 'RSA2');
    }
    public function createOrder($data){
        $alipay = $this->alipay();
        $instance = $alipay->getInstance();
        $order = new \AlipayTradePagePayRequest();
        if(!empty($data['t_return_url'])){
            $order->setReturnUrl($data['t_return_url']);
        }else{
            $order->setReturnUrl($alipay->returnUrl);
        }
        $order->setNotifyUrl($alipay->notifyUrl);
        $order->setBizContent('{"timeout_express":"'.$alipay->orderTimeOut.'","product_code":"FAST_INSTANT_TRADE_PAY","out_trade_no":"'.($data['t_out_trade_no']).'","subject":"'.($data['t_title']).'","total_amount":"'.(''.($data['t_fee']/100)).'","body":"'.($data['t_body']).'"}');

        if(Payment::USE_FOR_MOBILE == $data['t_info_type']){
            $payData = $instance->sdkExecute($order);
        }else{
            $payData = $instance->pageExecute($order, 'get');
        }
        return [$payData, null];
    }
}
