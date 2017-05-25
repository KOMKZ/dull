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
$url = $wxpay->createPrePayUrl($productId);
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
