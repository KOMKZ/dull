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

    public function actionRefund(){
        $payment = Yii::$app->alipay;

        $refund = $payment->createRefund();

        $refund->batch_no = (date("YmdHis",time())).'001';
        $refund->addOneRefund('2017022721001004880233243222', '0.02', '课程退款');

        $refundUrl = $payment->buildRefundUrl($refund);
        console($refundUrl);
    }

    public function actionDemo(){
        $payment = Yii::$app->alipay;

        $payOrder = $payment->createOrder();
        /**
         * 20170227100231,
         * 20170227100232
         */
        $payOrder->out_trade_no = '20170227100234';
        $payOrder->total_fee = '0.01';
        $payOrder->subject = '安全家.安全防爆电器课程';
        $payOrder->body = '安全家.安全防爆电器课程';
        $payOrder->it_b_pay = '1m';

        $url = $payment->buildOrderUrl($payOrder);
        console($url);
    }
}
