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
class WxOrder extends WxApi
{
    const API_URL = "https://api.mch.weixin.qq.com/pay/unifiedorder";
    private $goodsDetail = [];

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
    public function setDeviceInfo($value)
	{
		$this->values['device_info'] = $value;
	}
    public function getDeviceInfo(){
        return $this->values['device_info'];
    }
    public function hasDeviceInfo(){
        return array_key_exists('device_info', $this->values) && !empty($this->values['device_info']);
    }
    public function setBody($value)
    {
        $this->values['body'] = $value;
    }
    public function getBody(){
        return $this->values['body'];
    }
    public function hasBody(){
        return array_key_exists('body', $this->values) && !empty($this->values['body']);
    }
    public function setDetail($value)
    {
        $this->values['detail'] = $value;
    }
    public function getDetail(){
        return $this->values['detail'];
    }
    public function hasDetail(){
        return array_key_exists('detail', $this->values) && !empty($this->values['detail']);
    }
    public function setAttach($value)
    {
        $this->values['attach'] = $value;
    }
    public function getAttach(){
        return $this->values['attach'];
    }
    public function hasAttach(){
        return array_key_exists('attach', $this->values) && !empty($this->values['attach']);
    }
    public function setOutTradeNo($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    public function getOutTradeNo(){
        return $this->values['out_trade_no'];
    }
    public function hasOutTradeNo(){
        return array_key_exists('out_trade_no', $this->values) && !empty($this->values['out_trade_no']);
    }
    public function setFeeType($value)
    {
        $this->values['fee_type'] = $value;
    }
    public function getFeeType(){
        return $this->values['fee_type'];
    }
    public function hasFeeType(){
        return array_key_exists('fee_type', $this->values) && !empty($this->values['fee_type']);
    }
    public function setTotalFee($value)
    {
        $this->values['total_fee'] = $value;
    }
    public function getTotalFee(){
        return $this->values['total_fee'];
    }
    public function hasTotalFee(){
        return array_key_exists('total_fee', $this->values) && !empty($this->values['total_fee']);
    }
    public function setSpbillCreateIp($value)
    {
        $this->values['spbill_create_ip'] = $value;
    }
    public function getSpbillCreateIp(){
        return $this->values['spbill_create_ip'];
    }
    public function hasSpbillCreateIp(){
        return array_key_exists('spbill_create_ip', $this->values) && !empty($this->values['spbill_create_ip']);
    }
    public function setTimeStart($value)
    {
        $this->values['time_start'] = $value;
    }
    public function getTimeStart(){
        return $this->values['time_start'];
    }
    public function hasTimeStart(){
        return array_key_exists('time_start', $this->values) && !empty($this->values['time_start']);
    }
    public function setTimeExpire($value)
    {
        $this->values['time_expire'] = $value;
    }
    public function getTimeExpire(){
        return $this->values['time_expire'];
    }
    public function hasTimeExpire(){
        return array_key_exists('time_expire', $this->values) && !empty($this->values['time_expire']);
    }
    public function setGoodsTag($value)
    {
        $this->values['goods_tag'] = $value;
    }
    public function getGoodsTag(){
        return $this->values['goods_tag'];
    }
    public function hasGoodsTag(){
        return array_key_exists('goods_tag', $this->values) && !empty($this->values['goods_tag']);
    }
    public function setNotifyUrl($value)
    {
        $this->values['notify_url'] = $value;
    }
    public function getNotifyUrl(){
        return $this->values['notify_url'];
    }
    public function hasNotifyUrl(){
        return array_key_exists('notify_url', $this->values) && !empty($this->values['notify_url']);
    }
    public function setTradeType($value)
    {
        $this->values['trade_type'] = $value;
    }
    public function getTradeType(){
        return $this->values['trade_type'];
    }
    public function hasTradeType(){
        return array_key_exists('trade_type', $this->values) && !empty($this->values['trade_type']);
    }
    public function setOpenid($value)
    {
        $this->values['openid'] = $value;
    }
    public function getOpenid(){
        return $this->values['openid'];
    }
    public function hasOpenid(){
        return array_key_exists('openid', $this->values) && !empty($this->values['openid']);
    }

    public function createGoodsDetail(){
        return new GoodsDetail();
    }

    public function addGoodsDetail($goodsDetail){
        $values = $goodsDetail->getValues();
        if(!empty($values)){
            $this->goodsDetail[] = $values;
        }
    }

    public function saveGoodsDetail(){
        if(!empty($this->goodsDetail)){
            $data = json_encode($this->goodsDetail);
            $this->setDetail($data);
        }
    }

    public function getValues(){
        $this->setAppId(WxPay::$appId);
        $this->setMchId(WxPay::$mchId);
        if(!$this->hasNonceStr()){
            $this->setNonceStr(String::getNonceStr());
        }
        if(!$this->hasSpbillCreateIp()){
            // todo
            $this->SetSpbillCreateIp('127.0.0.1');
        }
        $this->signData();

        return $this->values;
    }

    public function send($timeout = 6){

		// 检测必填参数
		if(!$this->hasOutTradeNo()) {
			throw new \Exception("缺少统一支付接口必填参数out_trade_no！");
		}else if(!$this->hasBody()){
			throw new \Exception("缺少统一支付接口必填参数body！");
		}else if(!$this->hasTotalFee()) {
			throw new \Exception("缺少统一支付接口必填参数total_fee！");
		}else if(!$this->hasTradeType()) {
			throw new \Exception("缺少统一支付接口必填参数trade_type！");
		}
		// 关联参数
		if($this->getTradeType() == "JSAPI" && !$this->hasOpenid()){
			throw new \Exception("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
		}
		if($this->getTradeType() == "NATIVE" && !$this->hasProductId()){
			throw new \Exception("统一支付接口中，缺少必填参数product_id！trade_type为NATIVE时，product_id为必填参数！");
		}

		$xml = XmlHelper::arrayToXml($this->getValues());
		$response = $this->postXmlCurl($xml, self::API_URL, false, $timeout);

		// $result = WxPayResults::Init($response);
        return $response;
    }

}
