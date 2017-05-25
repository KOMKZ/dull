<?php
namespace wxpay\api;

use wxpay\WxApi;
use wxpay\helpers\String;
use wxpay\helpers\XmlHelper;
use wxpay\WxPay;
/**
 *
 */
class WxRefundQuery extends WxApi{
    CONST API_URL = 'https://api.mch.weixin.qq.com/pay/refundquery';

    public function setDeviceInfo($value){
        return $this->values['device_info'] = $value;
    }
    public function getDeviceInfo(){
        return $this->values['device_info'];
    }
    public function hasDeviceInfo(){
        return array_key_exists('device_info', $this->values) && !empty($this->values['device_info']);
    }



    public function setTransactionId($value){
        return $this->values['transaction_id'] = $value;
    }
    public function getTransactionId(){
        return $this->values['transaction_id'];
    }
    public function hasTransactionId(){
        return array_key_exists('transaction_id', $this->values) && !empty($this->values['transaction_id']);
    }



    public function setOutTradeNo($value){
        return $this->values['out_trade_no'] = $value;
    }
    public function getOutTradeNo(){
        return $this->values['out_trade_no'];
    }
    public function hasOutTradeNo(){
        return array_key_exists('out_trade_no', $this->values) && !empty($this->values['out_trade_no']);
    }


    public function setOutRefundNo($value){
        $this->values['out_refund_no'] = $value;
    }
    public function getOutRefundNo(){
        return $this->values['out_refund_no'];
    }
    public function hasOutRefundNo(){
        return array_key_exists('out_refund_no', $this->values) && !empty($this->values['out_refund_no']);
    }

    public function setRefundId($value){
        $this->values['refund_id'] = $value;
    }
    public function getRefundId(){
        return $this->values['refund_id'];
    }
    public function hasRefundId(){
        return array_key_exists('refund_id', $this->values) && !empty($this->values['refund_id']);
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
