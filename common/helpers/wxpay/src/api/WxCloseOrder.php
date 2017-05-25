<?php
namespace wxpay\api;

use wxpay\WxApi;
use wxpay\helpers\String;
use wxpay\helpers\XmlHelper;
use wxpay\WxPay;
use wxpay\api\GoodsDetail;
/**
 *
 */
class WxCloseOrder extends WxApi{
    const API_URL = 'https://api.mch.weixin.qq.com/pay/closeorder';
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
        if(!$this->hasOutTradeNo()) {
            throw new \Exception("订单关闭接口中，out_trade_no必填！");
        }

        $xml = XmlHelper::arrayToXml($this->getValues());
        $response = $this->postXmlCurl($xml, self::API_URL, false, $timeout);
        return $response;
    }
}
