<?php
namespace wxpay\api;

use wxpay\WxApi;

/**
 *
 */
class WxNotifyReply extends WxApi
{
    public function setReturnCode($value){
        $this->values['return_code'] = $value;
    }
    public function getReturnCode(){
        return $this->values['return_code'];
    }
    public function hasReturnCode(){
        return array_key_exists('return_code', $this->values) && !empty($this->values['return_code']);
    }
    public function setReturnMsg($value){
        $this->values['return_msg'] = $value;
    }
    public function getReturnMsg(){
        return $this->values['return_msg'];
    }
    public function hasReturnMsg(){
        return array_key_exists('return_msg', $this->values) && !empty($this->values['return_msg']);
    }




}
