<?php
namespace wxpay;

use wxpay\WxPay;
use wxpay\WxResponse;
use wxpay\helpers\XmlHelper;
/**
 *
 */
abstract class WxApi
{
    protected $values = [];

    public function getValues(){
        return $this->values;
    }

    public function setData($key, $value){
        $this->values[$key] = $value;
    }

    public function signData(){
        $signature = WxPay::getDataSignature($this->values);

        $this->setSign($signature);
    }

    public function setSign($value){
        $this->values['sign'] = $value;
    }
    public function getSign(){
        return $this->values['sign'];
    }
    public function hasSign(){
        return array_key_exists('sign', $this->values) && !empty($this->values['appid']);
    }

    public function setAppId($value){
        $this->values['appid'] = $value;
    }
    public function getAppId(){
        return $this->values['appid'];
    }
    public function hasAppId(){
        return array_key_exists('appid', $this->values) && !empty($this->values['appid']);
    }

    public function setMchId($value){
        $this->values['mch_id'] = $value;
    }
    public function getMchId(){
        return $this->values['mch_id'];
    }
    public function hasMchId(){
        return array_key_exists('mch_id', $this->values) && !empty($this->values['mch_id']);
    }

    public function setNonceStr($value){
        $this->values['nonce_str'] = $value;
    }
    public function getNonceStr(){
        return $this->values['nonce_str'];
    }
    public function hasNonceStr(){
        return array_key_exists('nonce_str', $this->values) && !empty($this->values['nonce_str']);
    }

    public function setTimeStamp($value){
        $this->values['time_stamp'] = $value;
    }
    public function getTimeStamp(){
        return $this->values['time_stamp'];
    }
    public function hasTimeStamp(){
        return array_key_exists('time_stamp', $this->values) && !empty($this->values['time_stamp']);
    }
    public function send($timeout = 30){

    }
    public function response(){
        ob_clean();
        return XmlHelper::arrayToXml($this->getValues());
    }
    protected function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        // //如果有配置代理这里就设置代理
        // if(WxPayConfig::CURL_PROXY_HOST != "0.0.0.0"
        //     && WxPayConfig::CURL_PROXY_PORT != 0){
        //     curl_setopt($ch,CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
        //     curl_setopt($ch,CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
        // }
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, WxPay::$sslcertPath);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, WxPay::$sslkeyPath);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return new WxResponse($data);
        } else {
            $error = curl_errno($ch);
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new \Exception("微信请求返回数据为空");
        }
    }


}
