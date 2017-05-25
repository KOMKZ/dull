<?php
namespace wxpay\api;

use wxpay\api\WxNotifyReply;
use wxpay\WxPay;
use wxpay\helpers\String;

/**
 *
 */
class WxOrderNotifyReply extends WxNotifyReply
{
    public function setPrepayId($value){
        $this->values['prepay_id'] = $value;
    }
    public function getPrepayId(){
        return $this->values['prepay_id'];
    }
    public function hasPrepayId(){
        return array_key_exists('prepay_id', $this->values) && !empty($this->values['prepay_id']);
    }
    public function getValues(){
        if(!$this->hasPrepayId()){
            throw new \Exception('The prepay id of order successful reply can\'t not be empty.');
        }
        $this->setAppId(WxPay::$appId);
        $this->setMchId(WxPay::$mchId);
        $this->setNonceStr(String::getNonceStr());
        $this->setData("result_code", "SUCCESS");
        $this->setData("err_code_des", "OK");
        $this->setReturnCode("SUCCESS");
        $this->setReturnMsg("OK");
        $this->signData();
        return $this->values;
    }


}
