<?php
namespace alipay;

use Yii;
use yii\base\Object;

/**
 *
 */
class AliPayment extends Object
{
    private $_instance = null;
    public $gatewayUrl;
    public $appId;
    public $rsaPrivateKeyFilePath;
    public $alipayrsaPublicKey;
    public $apiVersion = '1.0';
    public $signType = 'RSA2';
    public $postCharset = 'utf-8';
    public $format = 'json';
    public $notifyUrl = '';
    public $returnUrl = '';
    public $orderTimeOut = '30m';

    private $_payNotify;
    public function getPayNotify(){return $this->_payNotify;}

    private $_refundNotify;
    public function getRefundNotify(){return $this->_refundNotify;}

    public function buildOrderFromData($returnData){
        if($this->getInstance()->rsaCheckV1($returnData, $this->alipayrsaPublicKey, 'RSA2')){
            return $this->_payNotify = (object)$returnData;
        }else{
            return null;
        }
    }
    public function sendRefund($outTradeNo, $refundAmount, $refundReason = ''){
        $payment = $this->getInstance();
        $request = new \AlipayTradeRefundRequest ();
        $request->setBizContent("{" .
        "    \"trade_no\":\"{$outTradeNo}\"," .
        "    \"refund_amount\":{$refundAmount}," .
        "    \"refund_reason\":\"{$refundReason}\"" .
        "  }");
        $result = $payment->execute($request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            return [true, $result->$responseNode];
        } else {
            return [false, $result->$responseNode->sub_msg];
        }
    }
    public function queryOrder($id, $isOutTradeNo = true){
        $request = new \AlipayTradeQueryRequest();
        $request->setBizContent("{" .
        "    \"out_trade_no\":\"{$id}\"" .
        "  }");
        $result = $this->getInstance()->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            return $result->alipay_trade_query_response;
        } else {
            return null;
        }
    }
    public function getInstance(){
        if(is_object($this->_instance)){
            return $this->_instance;
        }
        $this->_instance = new \AopClient();
        $this->_instance->gatewayUrl = $this->gatewayUrl;
        $this->_instance->appId = $this->appId;
        $this->_instance->rsaPrivateKeyFilePath = $this->rsaPrivateKeyFilePath;
        $this->_instance->alipayrsaPublicKey = $this->alipayrsaPublicKey;
        $this->_instance->apiVersion = $this->apiVersion;
        $this->_instance->signType = $this->signType;
        $this->_instance->postCharset = $this->postCharset;
        $this->_instance->format = $this->format;
        return $this->_instance;
    }
}
