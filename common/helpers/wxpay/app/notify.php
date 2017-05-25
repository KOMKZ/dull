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
$notifyData = $wxpay->getRequestData();
$wxpay->debug($notifyData);
$wxpay->success();
