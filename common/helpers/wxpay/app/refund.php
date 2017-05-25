<?php
require './digitalnature/php-ref/ref.php';
require '../src/Autoloader.php';
error_reporting(E_ERROR);
Autoloader::register();
use wxpay\WxPay;

$config = require '/home/kitral/shuguang/config/config.php';

$wxpay = new WxPay(
    $config['appid'],
    $config['mchid'],
    $config['key'],
    $config['sslcert_path'],
    $config['sslkey_path']
);
// 必须于订单的信息完全匹配
$id = '4007132001201611281090075705';
$totalFee = 1;// 分
$refundFee = 1; // 分
$refundOrder = $wxpay->createRefund();
$refundOrder->setTransactionId($id);
$refundOrder->setTotalFee($totalFee);
$refundOrder->setRefundFee($refundFee);

// 重新分配的退款订单号
$refundOrder->setOutRefundNo($wxpay->getMchid().date("YmdHis"));
$refundOrder->setOpUserId($wxpay->getMchId());

$res = $wxpay->sendRefund($refundOrder);
if($res->isSuccess()){
    debug($res->getData());
}else{
    debug($res->getData(), $res->getErrMsg());
}
