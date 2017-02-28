<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController;
use Payment\QueryContext;
use Payment\Config;

/**
 *
 */
class AlipayController extends AdminController{
    public function actionA(){
        // 支付宝配置信息
        $aliconfig = [
            'partner'   => '2088812483293400',
            'sign_type' => 'md5',
            'md5_key'   => 'tmpztj8qacr6axz95kiouqtotoqexhal',
            "notify_url"	=> 'http://test.helei.com/pay-notify.html',
            "return_url"	=> 'http://test.helei.com/return-url.html',
            "time_expire"	=> '14',
        ];

        $data = [
            // 通过支付宝交易号查询，  推荐  效率更高
            'transaction_id'    => '2016011421001004330041239366',// 支付宝

            // 通过订单号查询
            'order_no'    => '2016011402433464',// 支付宝
        ];

        $query = new QueryContext();

        try {
            // 支付宝查询
            $query->initQuery(Config::ALI, $aliconfig);
            $ret = $query->query($data);

        } catch (PayException $e) {
            echo $e->errorMessage();exit;
        }
        console($ret);
    }
    public function actionRefund_notify(){
        $refundResult = [
            'sign' => '8882ad02735879169c01b7e530940054',
            'result_details' => '2017022721001004880233243222^0.02^REFUND_TRADE_FEE_ERROR',
            'notify_time' => '2017-02-27 17:35:03',
            'sign_type' => 'MD5',
            'notify_type' => 'batch_refund_notify',
            'notify_id' => '3f91ed3396455e934b4fba505f6568fj5y',
            'batch_no' => '20170227173243001',
            'success_num' => '0',
        ];
        $payment = Yii::$app->alipay;
        $refund = $payment->buildRefundFromData($refundResult);
        console($refund->toArray());
    }
    public function actionNotify(){
        Yii::error(1);
        // $notifyData = [
        //     'discount' => '0.00',
        //     'payment_type' => '1',
        //     'subject' => '安全家.安全防爆电器课程',
        //     'trade_no' => '2017022721001004880233070742',
        //     'buyer_email' => 'tianlongdiguo@hotmail.com',
        //     'gmt_create' => '2017-02-27 15:17:49',
        //     'notify_type' => 'trade_status_sync',
        //     'quantity' => '1',
        //     'out_trade_no' => '20170227100233',
        //     'seller_id' => '2088812483293400',
        //     'notify_time' => '2017-02-27 15:17:58',
        //     'body' => '安全家.安全防爆电器课程',
        //     'trade_status' => 'TRADE_SUCCESS',
        //     'is_total_fee_adjust' => 'N',
        //     'total_fee' => '0.01',
        //     'gmt_payment' => '2017-02-27 15:17:58',
        //     'seller_email' => 'alicloud@trainor.cn',
        //     'price' => '0.01',
        //     'buyer_id' => '2088302401130884',
        //     'notify_id' => '1e88c7c03f0458ce97cbb33fb720bd9msi',
        //     'use_coupon' => 'N',
        //     'sign_type' => 'MD5',
        //     'sign' => 'b8865f37153a1c83898c263e7a0dcfb9'
        // ];
        // $payment = Yii::$app->alipay;
        // //
        // $result = $payment->buildOrderFromData($notifyData);
    }
    public function actionReturn() {
        Yii::error(2);
        // $notifyData = [
        //     'body' => '安全家.安全防爆电器课程',
        //     'buyer_email' => 'tianlongdiguo@hotmail.com',
        //     'buyer_id' => '2088302401130884',
        //     'exterface' => 'create_direct_pay_by_user',
        //     'is_success' => 'T',
        //     'notify_id' => 'RqPnCoPT3K9%2Fvwbh3InZezaqbcffUf1lCTd8%2F%2BSJMJ3tcHqhJh2wu0K50RwNUtRV8Bm%2B',
        //     'notify_time' => '2017-02-27 13:47:04',
        //     'notify_type' => 'trade_status_sync',
        //     'out_trade_no' => '20170227100231',
        //     'payment_type' => '1',
        //     'seller_email' => 'alicloud@trainor.cn',
        //     'seller_id' => '2088812483293400',
        //     'subject' => '安全家.安全防爆电器课程',
        //     'total_fee' => '0.01',
        //     'trade_no' => '2017022721001004880232929740',
        //     'trade_status' => 'TRADE_SUCCESS',
        //     'sign' => '0320112fb2e18329be039f3860394055',
        //     'sign_type' => 'MD5',
        // ];
        //
        // $payment = Yii::$app->alipay;

        //
        // $order = $payment->buildOrderFromData($notifyData);
        //
        // console($order->isCompleted);
    }
}
