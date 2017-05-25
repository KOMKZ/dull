<?php
namespace wxpay\api;

use wxpay\WxApi;
use wxpay\helpers\String;
use wxpay\WxPay;
/**
 *
 */
class PrePay extends WxApi
{
    public function setProductId($value){
        if(empty($value)){
            throw new \Exception("ProductId can't not be empty");
        }
        $this->values['product_id'] = $value;
    }
    public function getProductId($value){
        return $this->values['product_id'];
    }
    public function hasProductId(){
        return array_key_exists('product_id', $this->values) && !empty($this->values['product_id']);
    }
    public function getValues(){
        $this->setAppId(WxPay::$appId);
        $this->setMchId(WxPay::$mchId);
        if(!$this->hasNonceStr()){
            $this->setNonceStr(String::getNonceStr());
        }
        if(!$this->hasTimeStamp()){
            $this->setTimeStamp(time());
        }
        $this->signData();
        return $this->values;
    }

}
