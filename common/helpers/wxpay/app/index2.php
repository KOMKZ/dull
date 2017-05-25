<?php
require './digitalnature/php-ref/ref.php';
require '../src/Autoloader.php';

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

$productId = '123456';
$wxOrder = $wxpay->createOrder();
// 可选
// $wxOrder->setOpenid($notifyData['openid']);
$wxOrder->setProductId($productId);
// body: String(128)
// 商品描述 https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=4_2
$wxOrder->setBody("泰致德安全家官网：积分充值卡");
// attach：String(127)
// 附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
$wxOrder->setAttach("100元充值卡");
// 商户订单号 重要，该字段可用来查询订单详情
// 商户支付的订单号由商户自定义生成，微信支付要求商户订单号保持唯一性
// productId可以在notifyData中拿到, 根据productId生成订单id或者找到订单id
$outTradeNo = $wxpay->getMchid().date("YmdHis");
$wxOrder->setOutTradeNo($outTradeNo);

// 支付金额 单位为分，参数值不能带小数。而对账单中的交易金额单位为【元】
$wxOrder->setTotalFee("1");
// 订单生成时间
$wxOrder->setTimeStart(date("YmdHis"));
// 订单失效时间 最短失效时间间隔必须大于5分钟
$wxOrder->setTimeExpire(date("YmdHis", time() + 600));
// 用途带测
$wxOrder->setGoodsTag("test");
$wxOrder->setNotifyUrl("http://trainor.xicp.net:22103/wxpay_new/app/notify.php");



$detail = $wxOrder->createGoodsDetail();

$detail->setGoodsId(000001);
$detail->setWxpayGoodsId(000002);
$detail->setGoodsName('积分充值卡');
$detail->setQuantity(10);
$detail->setPrice(1);
$detail->setGoodsCategory('test_goods');
$detail->setBody('积分充值卡的详细介绍信息');

$wxOrder->addGoodsDetail($detail);
$wxOrder->saveGoodsDetail();

$res = $wxpay->sendOrder($wxOrder);
if($res->isSuccess()){
    $url = urlencode($res->getValue('code_url'));
}else{
    debug($res->getErrMsg());
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <h2>扫码支付</h2>
        <img style="margin:600px 800px;" src="./qrcode.php?data=<?= $url;?>" alt="" />
    </body>
</html>
