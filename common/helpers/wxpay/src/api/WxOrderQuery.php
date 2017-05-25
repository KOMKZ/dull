<?php
namespace wxpay\api;

use wxpay\WxApi;
use wxpay\helpers\String;
use wxpay\helpers\XmlHelper;
use wxpay\WxPay;
/**
 *
 */
class WxOrderQuery extends WxApi{
    CONST API_URL = 'https://api.mch.weixin.qq.com/pay/orderquery';
    public function setTransactionId($value){
        $this->values['transaction_id'] = $value;
    }
    public function getTransactionId(){
        return $this->values['transaction_id'];
    }
    public function hasTransactionId(){
        return array_key_exists('transaction_id', $this->values) && !empty($this->values['transaction_id']);
    }
    public function setOutTradeNo($value){
        $this->values['out_trade_no'] = $value;
    }
    public function getOutTradeNo(){
        return $this->values['out_trade_no'];
    }
    public function hasOutTradeNo(){
        return array_key_exists('out_trade_no', $this->values) && !empty($this->values['out_trade_no']);
    }
    public function getValues(){
        $this->setAppId(WxPay::$appId);
        $this->setMchId(WxPay::$mchId);
        $this->setNonceStr(String::getNonceStr());
        $this->signData();
        return $this->values;
    }
    public function send($timeout = 6){
        $xml = XmlHelper::arrayToXml($this->getValues());
        $response = $this->postXmlCurl($xml, self::API_URL, false, $timeout);
        return $response;
    }




}
