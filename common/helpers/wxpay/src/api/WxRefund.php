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
class WxRefund extends WxApi{
    const API_URL = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

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
        return $this->values['out_refund_no'] = $value;
    }
    public function getOutRefundNo(){
        return $this->values['out_refund_no'];
    }
    public function hasOutRefundNo(){
        return array_key_exists('out_refund_no', $this->values) && !empty($this->values['out_refund_no']);
    }



    public function setTotalFee($value){
        return $this->values['total_fee'] = $value;
    }
    public function getTotalFee(){
        return $this->values['total_fee'];
    }
    public function hasTotalFee(){
        return array_key_exists('total_fee', $this->values) && !empty($this->values['total_fee']);
    }



    public function setRefundFee($value){
        return $this->values['refund_fee'] = $value;
    }
    public function getRefundFee(){
        return $this->values['refund_fee'];
    }
    public function hasRefundFee(){
        return array_key_exists('refund_fee', $this->values) && !empty($this->values['refund_fee']);
    }



    public function setRefundFeeType($value){
        return $this->values['refund_fee_type'] = $value;
    }
    public function getRefundFeeType(){
        return $this->values['refund_fee_type'];
    }
    public function hasRefundFeeType(){
        return array_key_exists('refund_fee_type', $this->values) && !empty($this->values['refund_fee_type']);
    }



    public function setOpUserId($value){
        return $this->values['op_user_id'] = $value;
    }
    public function getOpUserId(){
        return $this->values['op_user_id'];
    }
    public function hasOpUserId(){
        return array_key_exists('op_user_id', $this->values) && !empty($this->values['op_user_id']);
    }

    public function getValues(){
        $this->setAppId(WxPay::$appId);
        $this->setMchId(WxPay::$mchId);
        $this->setNonceStr(String::getNonceStr());
        $this->signData();
        return $this->values;
    }
    public function send($timeout = 6){
		//检测必填参数
		if(!$this->hasOutTradeNo() && !$this->hasTransactionId()) {
			throw new \Exception("退款申请接口中，out_trade_no、transaction_id至少填一个！");
		}else if(!$this->hasOutRefundNo()){
			throw new \Exception("退款申请接口中，缺少必填参数out_refund_no！");
		}else if(!$this->hasTotalFee()){
			throw new \Exception("退款申请接口中，缺少必填参数total_fee！");
		}else if(!$this->hasRefundFee()){
			throw new \Exception("退款申请接口中，缺少必填参数refund_fee！");
		}else if(!$this->hasOpUserId()){
			throw new \Exception("退款申请接口中，缺少必填参数op_user_id！");
		}


        $xml = XmlHelper::arrayToXml($this->getValues());
        $response = $this->postXmlCurl($xml, self::API_URL, true, $timeout);
        return $response;
    }


}
