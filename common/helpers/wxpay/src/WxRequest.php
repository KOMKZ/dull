<?php
namespace wxpay;

use wxpay\WxPay;
use wxpay\helpers\XmlHelper;
/**
 *
 */
class WxRequest
{
    private $_data;
    // public function __construct(){
    //     $this->_data = $this->resolve();
    // }
    public function getData(){
        return $this->_data;
    }
    public function setData($value){
        $this->_data = $this->resolve($value);
        // if(!WxPay::checkDataValid($this->_data)){
        //     throw new \Exception('Access Invalid in resolving weixin request');
        // }
    }
    protected function resolve($data = null){
        if(!empty($data)){
            return XmlHelper::xmlToArray($data);
        }elseif(array_key_exists('HTTP_RAW_POST_DATA', $GLOBALS)){
            return XmlHelper::xmlToArray($GLOBALS['HTTP_RAW_POST_DATA']);
        }else{
            return null;
        }
    }
}
