<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use common\models\email\EmailModel;
use alipay\AliPayment;
use yii\helpers\ArrayHelper;


/**
 *
 */
class DemoController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionAlipayapi(){
        return $this->renderPartial('alipayapi');
    }
    public function actionIndex(){
        return $this->renderPartial('index');
    }


    public function actionDemo(){
        $payment = Yii::$app->alipay;

        $payOrder = $payment->createOrder();
        /**
         * 20170227100231,
         * 20170227100232
         */
        $payOrder->out_trade_no = '20170227100233';
        $payOrder->total_fee = '0.01';
        $payOrder->subject = '安全家.安全防爆电器课程';
        $payOrder->body = '安全家.安全防爆电器课程';
        $payOrder->it_b_pay = '1m';
        $url = $payment->buildOrderUrl($payOrder);
        console($url);
    }
}
