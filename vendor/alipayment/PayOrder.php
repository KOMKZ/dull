<?php
namespace alipay;


use yii\base\Object;
use yii\helpers\ArrayHelper;


/**
 *
 */
class PayOrder extends Object
{
    public function toArray(){
        return ArrayHelper::toArray($this);
    }

    /**
    * 说明: 商户网站唯一订单号
    * 是否可空：不可空
    * 描述：支付宝合作商户网站唯一订单号。
    */
    public $out_trade_no;

    /**
    * 说明: 商品名称
    * 是否可空：不可空
    * 描述：商品的标题/交易标题/订单标题/订单关键字等。</p> <p>该参数最长为128个汉字。
    */
    public $subject;



    /**
    * 说明: 交易金额
    * 是否可空：不可空
    * 描述：该笔订单的资金总额，单位为RMB-Yuan。取值范围为[0.01，100000000.00]，精确到小数点后两位。
    */
    public $total_fee;


    /**
    * 说明: 买家支付宝用户号
    * 是否可空：
    * 描述：可空
    */
    public $buyer_id;

    /**
    * 说明: 买家支付宝账号
    * 是否可空：
    * 描述：可空
    */
    public $buyer_email;

    /**
    * 说明: 买家支付宝账号别名
    * 是否可空：
    * 描述：可空
    */
    public $buyer_account_name;

    /**
    * 说明: 商品单价
    * 是否可空：可空
    * 描述：单位为：RMB Yuan。取值范围为[0.01，100000000.00]，精确到小数点后两位。此参数为单价</p>
    * <p>规则：price、quantity能代替total_fee。即存在total_fee，就不能存在price和quantity；存在price、quantity，就不能存在total_fee。
    */
    public $price;

    /**
    * 说明: 购买数量
    * 是否可空：可空
    * 描述：price、quantity能代替total_fee。即存在total_fee，就不能存在price和quantity；存在price、quantity，就不能存在total_fee。
    */
    public $quantity;

    /**
    * 说明: 商品描述
    * 是否可空：可空
    * 描述：
    */
    public $body;

    /**
    * 说明: 商品展示网址
    * 是否可空：可空
    * 描述：>收银台页面上，商品展示的超链接。
    */
    public $show_url;

    /**
    * 说明: 默认支付方式
    * 是否可空：可空
    * 描述：如果不设置，默认识别为余额支付。
    */
    public $paymethod;

    /**
    * 说明: 可用渠道
    * 是否可空：可空
    * 描述：
    */
    public $enable_paymethod;

    /**
    * 说明: 禁用渠道
    * 是否可空：可空
    * 描述：
    */
    public $disable_paymethod;


    /**
    * 说明: 公用回传参数
    * 是否可空：可空
    * 描述：
    */
    public $extra_common_param;



    /**
    * 说明: 快捷登录授权令牌
    * 是否可空：可空
    * 描述：
    */
    public $token;

    /**
    * 说明: 扫码支付方式
    * 是否可空：可空
    * 描述：
    */
    public $qr_pay_mode;



    /**
    * 说明: 是否需要买家实名认证
    * 是否可空：可空
    * 描述：
    */
    public $need_buyer_realnamed;

    /**
    * 说明: 花呗分期参数
    * 是否可空：可空
    * 描述：
    */
    public $hb_fq_param;

    /**
    * 说明: 商品类型
    * 是否可空：可空
    * 描述：
    */
    public $goods_type;

    /**
    * 说明: 业务扩展参数
    * 是否可空：可空
    * 描述：
    */
    public $extend_param;

    /**
    * 说明: 订单的超时时间
    * 是否可空：可空
    * 描述：
    */
    public $it_b_pay;



    public $trade_status;
    public $trade_no;

    public function getIsCompleted(){
        return in_array($this->trade_status, ['TRADE_FINISHED', 'TRADE_SUCCESS']);
    }















}
