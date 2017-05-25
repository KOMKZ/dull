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
$id = '4007132001201611281090075705';
$res = $wxpay->queryOrder($id);
debug($res->getData());
?>
