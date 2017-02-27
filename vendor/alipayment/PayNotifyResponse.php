<?php
namespace alipay;

use yii\base\Object;

/**
 *
 */
class PayNotifyResponse extends Object
{
    public $is_success;
    public $sign_type;
    public $sign;
    public $out_trade_no;
    public $subject;
    public $payment_type;
    public $exterface;
    public $trade_no;
    public $trade_status;
    public $notify_id;
    public $notify_time;
    public $notify_type;
    public $seller_email;
    public $buyer_email;
    public $seller_id;
    public $buyer_id;
    public $total_fee;
    public $body;
    public $extra_common_param;

    public $gmt_create;
    public $gmt_payment;
    public $gmt_close;
    public $refund_status;
    public $gmt_refund;
    public $price;
    public $quantity;
    public $discount;
    public $is_total_fee_adjust;
    public $use_coupon;
    public $business_scene;

}
