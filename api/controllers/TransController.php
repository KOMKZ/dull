<?php
namespace api\controllers;

use Yii;
use common\base\ApiController;
use common\models\order\TransModel;
use common\models\order\tables\Trans;
use common\models\payment\tables\Payment;
use common\models\payment\PayModel;

/**
 *
 */
class TransController extends ApiController
{
    public function actionNotify($type){
        // Yii::error(Yii::$app->request->getBodyParams());
        // Yii::error(file_get_contents('php://input'));
        $t = Yii::$app->db->beginTransaction();
        $notifyData = Yii::$app->request->getBodyParams();
        $type = "alipay";
        $notifyData = [
            'total_amount' => '0.01',
            'buyer_id' => '2088102169564561',
            'trade_no' => '2017052621001004560200238659',
            'body' => 'text transaction',
            'notify_time' => '2017-05-26 18:15:16',
            'subject' => 'text transaction',
            'sign_type' => 'RSA2',
            'auth_app_id' => '2016101000649447',
            'charset' => 'utf-8',
            'notify_type' => 'trade_status_sync',
            'invoice_amount' => '0.01',
            'out_trade_no' => 'T1495779595824294',
            'trade_status' => 'TRADE_SUCCESS',
            'gmt_payment' => '2017-05-26 18:15:14',
            'version' => '1.0',
            'point_amount' => '0.00',
            'sign' => 'WwZASuAN3Vo7R3ajS1m4y34UIU3v+4tt3nd6jSAzInnsKnG3cDtG8Stp2I03b82hm6ao3XE6JqcoU2eCtHruFN6YIgReSj/llNIs/UNnwArNfHvTXQf+qJzGv4T36eyV9g/c3Dg/yRft5W84J//j9OVvsFGEgZjA6vgi28tGIuBobjfy0tmfagE769iW8fINORo74mmE2t5oF1W42C96WLdhwwQJZxXYUE7HjrbQpTtAfBXyoDB88l5LUQXTpSlc+TxvmmNFyi/OqTBHWuACb+Kn3cFehp0DX0tXsxoVWgdBl1YZtKkRcZ090JHtPUVIk7dokI0JlrlZg0B+dTdBmA==',
            'gmt_create' => '2017-05-26 18:15:04',
            'buyer_pay_amount' => '0.01',
            'receipt_amount' => '0.01',
            'fund_bill_list' => '[{"amount":"0.01","fundChannel":"ALIPAYACCOUNT"}]',
            'app_id' => '2016101000649447',
            'seller_id' => '2088102178864092',
            'notify_id' => '01297765b0f33473608529ce8b798eekbm',
        ];
        if(!in_array($type, [Payment::WXPAY, Payment::ALIPAY])){
            exit();
        }

        $pModel = new PayModel();
        // 校验
        $isDataValid = $pModel->validateData($notifyData, $type);
        if(!$isDataValid){
            exit();
        }
        // 再次查询是否已经支付
        list($isPayed, $thirdOrder) = $pModel->validatePayed($notifyData, $type);
        if(!$isPayed){
            exit();
        }
        $tModel = new TransModel();
        $trans = $tModel->getOne(['t_number' => $pModel->getTradeNo($notifyData, $type)]);
        if(!$trans){
            exit();
        }
        // todo交易本身是否已经支付
        $transData = [
            'fee' => $trans->t_fee
        ];
        // 和应用数据进行疲惫
        $isDataMatch = $pModel->checkDataMatch($thirdOrder, $transData, $type);
        if(!$isDataMatch){
            exit();
        }
        $appData = [
            't_succ_pay_type' => $type,
            't_notify_data' => $notifydata
        ];
        $r = $tModel->updateTransPayed($trans, $appData);
        if(!$r){
            list($code, $error) = $tModel->getOneError();
            console($code, $error);
            // todo

        }

    }
    public function actionCreatePayOrder(){
        $data = [
            't_number' => 'T1495779595824294',
            't_opr_uid' => 1,
            'po_type' => Payment::ALIPAY,
            'po_info_type' => Payment::USE_FOR_PC,
            't_pay_duration' => 3600,
        ];
        $pModel = new PayModel();
        $payOrder = $pModel->createPayOrder($data);
        if(!$payOrder){
            list($code, $msg) = $pModel->getOneError();
            console($code, $msg);
        }
        console($payOrder->toArray());

    }
    public function actionCreate(){
        $data = [
            't_title' => 'text transaction',
            't_type' => Trans::TYPE_CONSUME,
            't_fee_type' => Payment::TYPE_CNY,
            't_fee' => 1,
            't_pa_uid' => 1,
            't_pb_uid' => 2,
            't_des' => '',
            't_out_trade_no' => '1111118888'.mt_rand(11, 99),
            't_out_trade_type' => '1',
            't_app_id' => '1',
        ];
        $tModel = new TransModel();
        $trans = $tModel->createTrans($data);
        if(!$trans){
            list($code, $msg) = $tModel->getOneError();
            console($code, $msg);
        }
        console($trans->toArray());
    }
}
