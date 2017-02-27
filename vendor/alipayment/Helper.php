<?php
namespace alipay;

/**
 *
 */
class Helper
{
    public static function paraFilter($para) {
    	$para_filter = array();
    	while (list ($key, $val) = each ($para)) {
    		if($key == "sign" || $key == "sign_type" || $val == "")continue;
    		else	$para_filter[$key] = $para[$key];
    	}
    	return $para_filter;
    }

    public static function argSort($para) {
    	ksort($para);
    	reset($para);
    	return $para;
    }

    public static function createLinkstring($para) {
    	$arg  = "";
    	while (list ($key, $val) = each ($para)) {
    		$arg.=$key."=".$val."&";
    	}
    	//去掉最后一个&字符
    	$arg = substr($arg,0,count($arg)-2);

    	//如果存在转义字符，那么去掉转义
    	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

    	return $arg;
    }

    public static function rsaSign($data, $private_key) {
        //以下为了初始化私钥，保证在您填写私钥时不管是带格式还是不带格式都可以通过验证。
        $private_key=str_replace("-----BEGIN RSA PRIVATE KEY-----","",$private_key);
    	$private_key=str_replace("-----END RSA PRIVATE KEY-----","",$private_key);
    	$private_key=str_replace("\n","",$private_key);

    	$private_key="-----BEGIN RSA PRIVATE KEY-----".PHP_EOL .wordwrap($private_key, 64, "\n", true). PHP_EOL."-----END RSA PRIVATE KEY-----";

        $res=openssl_get_privatekey($private_key);

        if($res)
        {
            openssl_sign($data, $sign,$res);
        }
        else {
            throw new \Exception('The format of your private_key is incorrect!');
        }
        openssl_free_key($res);
    	//base64编码
        $sign = base64_encode($sign);
        return $sign;
    }

    public static function rsaVerify($data, $alipay_public_key, $sign)  {
        //以下为了初始化私钥，保证在您填写私钥时不管是带格式还是不带格式都可以通过验证。
    	$alipay_public_key=str_replace("-----BEGIN PUBLIC KEY-----","",$alipay_public_key);
    	$alipay_public_key=str_replace("-----END PUBLIC KEY-----","",$alipay_public_key);
    	$alipay_public_key=str_replace("\n","",$alipay_public_key);

        $alipay_public_key='-----BEGIN PUBLIC KEY-----'.PHP_EOL.wordwrap($alipay_public_key, 64, "\n", true) .PHP_EOL.'-----END PUBLIC KEY-----';
        $res=openssl_get_publickey($alipay_public_key);
        if($res)
        {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }
        else {
            throw new \Exception('The format of your alipay_public_key is incorrect!');
        }
        openssl_free_key($res);
        return $result;
    }


    public static function md5Sign($prestr, $key) {
    	$prestr = $prestr . $key;
    	return md5($prestr);
    }

    public static function md5Verify($prestr, $sign, $key) {
    	$prestr = $prestr . $key;
    	$mysign = md5($prestr);
    	if($mysign == $sign) {
    		return true;
    	}
    	else {
    		return false;
    	}
    }

    public static function getHttpResponsePOST($url, $cacert_url, $para, $input_charset = '') {

    	if (trim($input_charset) != '') {
    		$url = $url."_input_charset=".$input_charset;
    	}
    	$curl = curl_init($url);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
    	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
    	curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
    	curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
    	curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    	curl_setopt($curl,CURLOPT_POST,true); // post传输数据
    	curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
    	$responseText = curl_exec($curl);
    	//var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    	curl_close($curl);

    	return $responseText;
    }


    public static function getHttpResponseGET($url,$cacert_url) {
    	$curl = curl_init($url);
    	curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
    	curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
    	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
    	curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
    	$responseText = curl_exec($curl);
    	//var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    	curl_close($curl);

    	return $responseText;
    }
}
