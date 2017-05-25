<?php
namespace wxpay;

use yii\base\Object;
use wxpay\helpers\Dumper;
use wxpay\api\PrePay;
use wxpay\api\WxOrder;
use wxpay\api\WxOrderQuery;
use wxpay\WxRequest;
use wxpay\helpers\XmlHelper;
use wxpay\api\WxNotifyReply;
use wxpay\api\WxOrderNotifyReply;
use wxpay\api\WxRefund;
use wxpay\api\WxRefundQuery;
use wxpay\api\WxCloseOrder;
/**
 *
 */
class WxPay extends Object{

    CONST REFUND_TRADE_NO = 'out_trade_no';
    CONST REFUND_TRS_ID = 'transaction_id';
    CONST REFUND_REFUND_NO = 'out_refund_no';
    CONST REFUND_ID = 'refund_id';


    static public $appId;
    static public $mchId;
    static public $key;
    static public $sslcertPath;
    static public $sslkeyPath;
    private $defaultDebugFile = 'debug_output.txt';
    private $bizpayurl = 'weixin://wxpay/bizpayurl';
    private $notifyUrl = "";

    public $debug = true;
    static public $configValues = [];


    // public function __construct($appId, $mchId, $key = null, $sslcert_path = null, $sslkey_path, $config = []){
    //     $this->setAppId($appId);
    //     $this->setMchId($mchId);
    //     $this->setKey($key);
    //     $this->setSslcertPath($sslcert_path);
    //     $this->setSslkeyPath($sslkey_path);
    // }
    public function getConfigValues(){
        return self::$configValues = [
            'appid' => self::$appId,
            'mch_id' => self::$mchId,
            'key' => self::$key,
            'sslcert_path' => self::$sslcertPath,
            'sslkey_path' => self::$sslkeyPath
        ];
    }
    public function closeOrder($outTradeNo){
        // 用于返回正确信息，无论成功与否
        $request = new WxCloseOrder();
        $request->setOutTradeNo($outTradeNo);
        $response = $request->send();
        return $response;
    }
    public function createPrePayUrl($productId){
        $request = new PrePay();
        $request->setProductId($productId);
        return urlencode($this->bizpayurl. '?' . http_build_query($request->getValues()));
    }
    public function getRequestData($data = null){
        $request = new WxRequest();
        if(!empty($data)){
            $request->setData($data);
        }
        return $request->getData();
    }
    public function createOrder(){
        $wxOrder = new WxOrder();
        $wxOrder->setTradeType("NATIVE");
        return $wxOrder;
    }
    public function sendOrder(WxOrder $order, $timeOut = 6){
        if(!$order->hasNotifyUrl()){
        	$this->setNotifyUrl($this->notifyUrl);//异步通知url
        }

        $response = $order->send($timeOut);
        return $response;
    }

    public function queryOrder($orderId, $isTId = true){
        $orderQuery = new WxOrderQuery();
        if($isTId){
            $orderQuery->setTransactionId($orderId);
        }else{
            $orderQuery->setOutTradeNo($orderId);
        }
        return $orderQuery->send();
    }

    public function createRefund(){
        return new WxRefund();
    }
    public function sendRefund($refundOrder){
        $response = $refundOrder->send();
        return $response;
    }

    public function queryRefund($id, $type = self::REFUND_TRADE_NO){
        $query = new WxRefundQuery();
        switch ($type) {
            case self::REFUND_TRADE_NO:
                $query->SetOutTradeNo($id);
                break;
            case self::REFUND_TRS_ID:
                $query->SetTransactionId($id);
                break;
            case self::REFUND_REFUND_NO:
                $query->SetOutRefundNo($id);
                break;
            case self::REFUND_ID:
                $query->SetRefundId($id);
                break;
            default:
                throw new \Exception("Unsupported argument type value for querying refund.");
        }
        $response = $query->send();
        return $response;
    }




    public function error($msg){
        $notify = new WxNotifyReply();
        $notify->SetReturnCode("FAIL");
        $notify->SetReturnMsg($msg);
        $this->debug($notify->getValues());
        return $notify->response();
    }
    public function success(){
        $reply = new WxNotifyReply();
        $reply->SetReturnCode("SUCCESS");
        $reply->SetReturnMsg('OK');
        return $reply->response();
    }
    public function successOrderReply($prepayId){
        $reply = new WxOrderNotifyReply();
        $reply->setPrepayId($prepayId);
        $reply->response();
    }


    public function setAppId($value){
        if(empty($value)){
            throw new \Exception("The appId can't not be empty.");
        }
        self::$configValues['appid'] = self::$appId = $value;
    }
    public function setMchId($value){
        if(empty($value)){
            throw new \Exception("The mchId can't not be empty.");
        }
        self::$configValues['mch_id'] = self::$mchId = $value;
    }
    public function setKey($value){
        if(empty($value)){
            throw new \Exception("The key can't not be empty.");
        }
        self::$configValues['key'] = self::$key = $value;
    }
    public function setSslcertPath($value){
        if(!file_exists($value)){
            throw new \Exception("The specfied file path dons't exist. $value");
        }
        self::$configValues['sslcert_path'] = self::$sslcertPath = $value;
    }
    public function setSslkeyPath($value){
        if(!file_exists($value)){
            throw new \Exception("The specfied file path dons't exist. $value");
        }
        self::$configValues['sslkey_path'] = self::$sslkeyPath = $value;
    }
    public function getAppId(){
        return self::$appId;
    }
    public function getMchId(){
        return self::$mchId;
    }
    public function getKey(){
        return self::$key;
    }
    public function getSslcertPath(){
        return self::$sslcertPath;
    }
    public function getSslkeyPath(){
        return self::$sslkeyPath;
    }
    public function getNotifyUrl(){
        return $this->notifyUrl;
    }
    public function setNotifyUrl($value){
        $this->notifyUrl = $value;
    }



    public function debug($body, $title = ''){
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'runtime';
        $debugFile = $dir . DIRECTORY_SEPARATOR . 'debug_output.txt';
        $header = sprintf("%s\t%s", date('Y-m-d H:i:s', time()), $title);
        $body = trim(Dumper::export($body), "\n\t\s");
        $h = fopen($debugFile, 'a+');
        fwrite($h, sprintf("%s\n%s\n", $header, $body));
        fclose($h);
        chmod($debugFile, 0777);
    }

    public static function getDataSignature($values){

        ksort($values);
        $string = self::toUrlParams($values);
        $string = $string . "&key=".self::$key;
        $string = md5($string);
        $result = strtoupper($string);
        return $result;
    }
    public function checkDataValid($data){
        if(array_key_exists('sign', $data)){
            $sign = $data['sign'];
            unset($data['sign']);
            return $sign == self::getDataSignature($data);
        }
        return false;
    }
    protected static function toUrlParams($values)
    {
        $buff = "";
        foreach ($values as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

}
