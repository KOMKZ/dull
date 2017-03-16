<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController as Controller;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use common\models\email\EmailModel;
use alipay\AliPayment;
use yii\helpers\ArrayHelper;
use common\models\user\UserModel;
use common\models\notify\NotifyModel;
use common\models\notify\tables\SysMsg;
use yii\helpers\Url;
use common\models\file\FileModel;
use common\models\post\PostModel;


/**
 *
 */
class DemoController extends Controller
{
    public $enableCsrfValidation = false;



    public function actionDuandian(){

    }

    public function actionParse(){
        $fileModel = new FileModel();
        $content = file_get_contents('//var/www/html/dull/1.txt');
        $oldContent = file_get_contents('//var/www/html/dull/2.txt');
        $fileModel->setFileValidFromContent($content, $oldContent);
    }

    public function actionD(){
        return $this->render('d.php');
    }

    public function actionC(){
        $notifyModel = new NotifyModel();
        $msgData = [
            'sm_use_tpl' => 1,
            'sm_object_type' => SysMsg::FOCUS_PULL_MSG,
            'sm_object_id' => 1,
            'sm_tpl_type' => 'publist_post',
        ];
        $user = Yii::$app->user->identity;
        $notifyModel->createMsg($msgData, [
            '{focus_username}' => $user->u_username,
            '{post_title}' => '《进击的巨人》舞台剧预告 经典人设、场景逼真还原',
            '{post_url}' => Url::toRoute(['post/view', 'id' => 1])
        ]);
    }

    public function actionB(){
        $notifyModel = new NotifyModel();
        // test pull msg sql
        $result = $notifyModel->test(2);
        console($result);

    }

    public function actionA(){
        $userModel = new UserModel();
        // $result = $userModel->addUserUFocus(2, [1, 3, 4, 5], false);
        // console($result, $userModel->getOneError());
        // $result = $userModel->removeUserUFocus(2, [1, 3, 4, 5]);
        // console($result, $userModel->getOneError());

        list($provider, ) = $userModel->getUserUFans(1);
        $result = $provider->getModels();
        console($result);
        //
        console($userModel->hadOneFocus(2, 1));
    }

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
