<?php
namespace alipay;

use Yii;
use yii\base\Object;
use alipay\PayOrder;
use alipay\Refund;
use alipay\PayNotifyResponse;
use alipay\RefundNotifyResponse;
use alipay\Helper;

/**
 *
 */
class AliPayment extends Object
{
    /**
    *合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner
    */
    public $partner;



    /**
    * 收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
    */
    private $_seller_id;
    public function getSeller_id(){
        return $this->_seller_id ? $this->_seller_id : $this->partner;
    }
    public function setSeller_id($value){
        return $this->_seller_id = $value;
    }

    /**
    * 说明: 卖家支付宝账号别名
    * 是否可空：不可空/可空 三个参数至少必须传递一个。
    * 描述：seller_account_name是卖家支付宝账号别名。
    */
    public $seller_account_name;

    /**
    * 说明: 卖家支付宝账号
    * 是否可空：不可空/可空 三个参数至少必须传递一个。
    * 描述：seller_email是支付宝登录账号，格式一般是邮箱或手机号。
    */
    public $seller_email;

    /**
    * 商户的私钥,此处填写原始私钥去头去尾，RSA公私钥生成：https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.nBDxfy&treeId=58&articleId=103242&docType=1
    * ras 加密填写
    * @see _sign_type
    */
    public $private_key;

    /**
     * MD5密钥，安全检验码，由数字和字母组成的32位字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
     * md5加密 填写
     * @see _sign_type
     */
    public $key;

    public $order_time_out = '30m';

    /**
    *支付宝的公钥，查看地址：https://b.alipay.com/order/pidAndKey.htm
    */
    private $_alipay_public_key = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB';
    public function getAlipay_public_key(){return $this->_alipay_public_key;}

    /**
    *服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
    */
    public $notify_url;

    /**
    *页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
    */
    public $return_url;

    public $refund_notify_url;

    /**
    *签名方式
    */
    private $_sign_type = 'rsa';
    public function getSign_type(){return $this->_sign_type;}
    public function setSign_type($value){
        $this->_sign_type = trim(strtoupper($value));
    }


    /**
    *字符编码格式 目前支持 gbk 或 utf-8
    */
    private $_input_charset = 'utf-8';
    public function getInput_charset(){return $this->_input_charset;}
    public function setInput_charset($value){
        $this->_input_charset = strtolower($value);
    }

    /**
    *ca证书路径地址，用于curl中ssl校验
    */
    private $_cacert;
    public function getCacert(){
        return $this->_cacert ? $this->_cacert : dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cacert.pem';
    }
    public function setCacert($value){
        if(!file_exists($value)){
            throw new \Exception('ali cacert file does\'s exists. ' . $value);
        }
        $this->_cacert = $value;
    }


    /**
    * 访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
    */
    public $transport = 'http';

    /**
    * 支付类型 ，无需修改
    */
    private $_payment_type = 1;
    public function getPayment_type(){return $this->_payment_type;}




    /**
    * 防钓鱼时间戳  若要使用请调用类文件submit中的query_timestamp函数
    */
    public $anti_phishing_key = '';

    /**
    * 客户端的IP地址 非局域网的外网IP地址，如：221.0.0.1
    */
    public $exter_invoke_ip = '';



    /**
    * 说明: 商户自定二维码宽度
    * 是否可空：可空
    * 描述：
    */
    public $qrcode_width;


    /**
     * 支付宝网关地址（新）
     */
    private $_alipay_gateway_new = 'https://mapi.alipay.com/gateway.do?';

    /**
    * HTTPS形式消息验证地址
    */
    private $_https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';

    /**
    * HTTP形式消息验证地址
    */
    private $_http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';


    private $_pay_notify;
    public function getPay_notify(){return $this->_pay_notify;}

    private $_refund_notify;
    public function getRefund_notify(){return $this->_refund_notify;}



    public function createOrder(){
        return new PayOrder();
    }

    public function createRefund(){
        return new Refund();
    }

    public function buildOrderUrl(PayOrder $order){
        $para = $this->appendOrderPara($order->toArray());
        $para = $this->buildRequestPara($para);
        return $this->_alipay_gateway_new . http_build_query($para);
    }

    public function buildRefundUrl(Refund $refund){
        $para = $this->appendRefundPara($refund->toArray());
        $para = $this->buildRequestPara($para);
        return $this->_alipay_gateway_new . http_build_query($para);

    }

    /**
     * 通过通知的数据构建订单对象
     * @param  [type] $returnData 通知数据
     * @param  [type] $asyc       来自于return_url 还是 notify_url
     * @return [type]             [description]
     */
    public function buildOrderFromData($returnData){
        if($this->verify($returnData)){
            $notify = new PayNotifyResponse();
            $payOrder = new PayOrder();
            foreach($returnData as $name => $value){
                if($notify->canSetProperty($name)){
                    $notify->$name = $value;
                }
                if($payOrder->canSetProperty($name)){
                    $payOrder->$name = $value;
                }
            }
            $this->_pay_notify = $notify;
            return $payOrder;
        }else{
            return null;
        }
    }

    public function buildRefundFromData($refundResult){
        if($this->verify($refundResult)){
            $notify = new RefundNotifyResponse();
            $refund = new Refund();
            foreach($refundResult as $name => $value){
                if($notify->canSetProperty($name)){
                    $notify->$name = $value;
                }
                if($refund->canSetProperty($name)){
                    $refund->$name = $value;
                }
            }
            $this->_refund_notify = $notify;
            return $refund;
        }else{
            return null;
        }
    }

    public function success(){
        echo "success";
        exit();
    }

    public function verify($returnData){
		if(empty($returnData)) {
			return false;
		}
		else {
			//生成签名结果
			$isSign = $this->getSignVeryfy($returnData, $returnData["sign"]);
			//获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
			$responseTxt = 'false';
			if (! empty($returnData["notify_id"])) {$responseTxt = $this->getResponse($returnData["notify_id"]);}
			//验证
			//$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
			//isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
			if (preg_match("/true$/i",$responseTxt) && $isSign) {
				return true;
			} else {
				return false;
			}
		}
	}



    protected function appendOrderPara($para){
        $corePara = [
            'service' => 'create_direct_pay_by_user',
            'partner' => $this->partner,
            'seller_id' => $this->getSeller_id(),
            'payment_type' => $this->_payment_type,
            'return_url' => $this->return_url,
            'notify_url' => $this->notify_url,
            'anti_phishing_key' => $this->anti_phishing_key,
            'exter_invoke_ip' => $this->exter_invoke_ip,
            '_input_charset' => $this->_input_charset
        ];
        return array_merge($para, $corePara);
    }

    protected function appendRefundPara($para){
        $corePara = [
            'service' => 'refund_fastpay_by_platform_pwd',
            'partner' => $this->partner,
            'seller_email' => $this->seller_email,
            'notify_url' => $this->refund_notify_url,
            'refund_date' => date("Y-m-d H:i:s",time()),
            '_input_charset' => $this->_input_charset
        ];
        return array_merge($para, $corePara);
    }

    protected function buildRequestPara($para_temp) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = Helper::paraFilter($para_temp);

		//对待签名参数数组排序
		$para_sort = Helper::argSort($para_filter);

		//生成签名结果
		$mysign = $this->buildRequestMysign($para_sort);

		//签名结果与签名方式加入请求提交参数组中
		$para_sort['sign'] = $mysign;
		$para_sort['sign_type'] = $this->_sign_type;

		return $para_sort;
	}

    protected function buildRequestMysign($paraSort) {
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = Helper::createLinkstring($paraSort);
		$mysign = "";
		switch ($this->_sign_type) {
			case "RSA":
				$mysign = Helper::rsaSign($prestr, $this->private_key);
				break;
            case "MD5":
				$mysign = Helper::md5Sign($prestr, $this->key);
				break;
            default:
                throw new \Exception("Unsupport sign way.");
		}
		return $mysign;
    }

    protected function getSignVeryfy($para_temp, $sign) {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = Helper::paraFilter($para_temp);

        //对待签名参数数组排序
        $para_sort = Helper::argSort($para_filter);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = Helper::createLinkstring($para_sort);

        $isSign = false;
        switch ($this->_sign_type) {
            case "MD5" :
                $isSign = Helper::md5Verify($prestr, $sign, $this->key);
                break;
            case "RSA" :
				$isSign = Helper::saVerify($prestr, trim($this->_alipay_public_key), $sign);
				break;
            default :
                $isSign = false;
        }

        return $isSign;
    }

    protected function getResponse($notify_id) {
		$partner = trim($this->partner);
		$veryfy_url = '';
		if(trim($this->transport) == 'https') {
			$veryfy_url = $this->_https_verify_url;
		}
		else {
			$veryfy_url = $this->_http_verify_url;
		}
		$veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
		$responseTxt = Helper::getHttpResponseGET($veryfy_url, $this->_cacert);

		return $responseTxt;
    }
}
