<?php
namespace wxpay;

use wxpay\WxPay;
use wxpay\helpers\XmlHelper;
/**
 *
 */
class WxResponse
{
    private $data;
    public function __construct($response = null){
        $this->resolve($response);
    }
    public function getValue($name){
        if(array_key_exists($name, $this->data)){
            return $this->data[$name];
        }else{
            return null;
        }
    }
    public function getData(){
        return $this->data;
    }
    protected function resolve($response = null){
        $this->data = XmlHelper::xmlToArray($response);

    }
    public function getErrMsg(){
        if(array_key_exists('result_code', $this->data) && $this->data['result_code'] == 'FAIL'){
            return $this->data['err_code_des'];
        }elseif(array_key_exists('return_code', $this->data) && $this->data['return_code'] == 'FAIL'){
            return $this->data['return_msg'];
        }else{
            return null;
        }
    }
    public function isFail(){
        return !$this->isSuccess();
    }
    public function isSuccess(){
        if(array_key_exists("return_code", $this->data)
			&& array_key_exists("result_code", $this->data)
			&& $this->data["return_code"] == "SUCCESS"
			&& $this->data["result_code"] == "SUCCESS")
		{
			return true;
		}else{
            return false;
        }
    }
}
