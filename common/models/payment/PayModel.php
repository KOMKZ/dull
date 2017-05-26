<?php
namespace common\models\payment;

use Yii;
use common\base\Model;
use common\models\payment\tables\PayOrder;
use common\models\order\tables\Trans;
use common\models\order\TransModel;
use common\models\payment\Wxpay;
use common\models\payment\Alipay;
use common\models\payment\tables\Payment;
/**
 *
 */
class PayModel extends Model
{
    public function getOnePayOrder($condition){
        if(is_object($condition)){
            return $condition;
        }
        if($condition){
            return PayOrder::find()->where($condition)->one();
        }else{
            return null;
        }
    }



    /**
     * [createPayOrder description]
     * @param  [type] $data [description]
     * 't_number' => 'T1495779595824294', 交易编号，必须
     * 't_opr_uid' => 1, 操作用户，必须
     * 'po_type' => Payment::ALIPAY, 支付方式，必须
     * 'po_info_type' => Payment::USE_FOR_PC,， 信息类型，可选
     * 't_pay_duration' => 3600,有效期，可选,
     * 't_return_url' => '', 返回的地址链接，支付宝可以用这个参数
     * @return [type]       [description]
     */
    public function createPayOrder($data){
        $t = Yii::$app->db->beginTransaction();
        try {
            // 构建支付单数据并创建
            $payOrder = new PayOrder();
            $payOrder->scenario = 'create';
            if(!$payOrder->load($data, '') || !$payOrder->validate()){
                $this->addError('', $this->getArErrMsg($payOrder));
                return false;
            }
            if(empty($data['t_number'])){
                $this->addError('', 't_number is required');
                return false;
            }
            if(empty($data['t_opr_uid'])){
                $this->addError('', 't_opr_uid is required');
                return false;
            }
            if(empty($data['t_number'])){
                $this->addError('', 't_number is required');
                return false;
            }
            if(empty($data['t_pay_duration'])){
                $duration = self::getDfPoValidDuration();
            }else{
                $duration = $data['t_pay_duration'];
            }
            $tModel = new TransModel();
            $trans = $tModel->getOne(['t_number' => $data['t_number'], 't_type' => Trans::TYPE_CONSUME]);
            if(!$trans){
                $this->addError('', '指定的数据不存在');
                return false;
            }
            // 查询是否有该交易支付单存在
            $payOrder = $this->getOnePayOrder([
                'and',
                ['=', 'po_tid', $trans->t_id],
                ['=', 'po_type', $data['po_type']],
                ['=', 'po_info_type', $data['po_info_type']]
            ]);
            // 第三方数据
            $thirdPayData = [
                't_out_trade_no' => $trans->t_number,
                't_fee' => $trans->t_fee,
                't_info_type' => $data['po_info_type'],
                't_pay_type' => $data['po_type'],
                't_title' => $trans->t_title,
                't_return_url' => $data['t_return_url'],
                't_pay_duration' => $duration,
                't_body' => $trans->t_title,
            ];
            if($payOrder && $payOrder->po_invalid_after <= time()){
                // 已经过期，重新请求第三方数据
                $thirdPayData = $this->applyThirdPayData($thirdPayData);
                // 修改原来的数据
                $payOrder->po_third_data = $thirdPayData;
                $payOrder->po_invalid_after = $duration + time();
                if(false === $payOrder->update(false)){
                    $this->addError('', '创建支付单失败');
                    return false;
                }
            }elseif($payOrder && $payOrder->po_invalid_after > time()){
                // 未过期
                return $payOrder;
            }else{
                $payOrder = new PayOrder();
                // 不存在
                $payOrder->po_tid = $trans->t_id;
                $payOrder->po_invalid_after = $duration + time();
                $payOrder->po_pay_status = Payment::PS_NOT_PAY;
                $payOrder->po_error_status = Payment::PES_NOT_ERROR;
                if(!$payOrder->insert(false)){
                    $this->addError('', '创建支付单失败');
                    return false;
                }
                // 请求第三方数据
                $thirdPayData = $this->applyThirdPayData($thirdPayData);
                // 修改原来的数据
                $payOrder->po_third_data = $thirdPayData;
                if(false === $payOrder->update(false)){
                    $this->addError('', '创建支付单失败');
                    return false;
                }
            }
            $t->commit();
            return $payOrder;
        } catch (\Exception $e) {
            Yii::error($e);
            throw $e;
            $t->rollback();
            $this->addError('', '创建支付单发生异常');
            return false;
        }


    }

    /**
     * [applyThirdPayData description]
     * @param  [type] $thirdPayData [description]
     * 't_out_trade_no' => $trans->t_number,
     * 't_fee' => $trans->t_fee,
     * 't_info_type' => $payOrder->po_info_type,
     * 't_pay_type' => $payOrder->po_type,
     * 't_title' => $trans->t_title,
     * 't_return_url' => $data['t_return_url'],
     * 't_pay_duration' => $duration,  // 未开放todo
     * 't_body' => '',
     * @return [type]               [description]
     */
    public function applyThirdPayData($thirdPayData){
        // todo 膨胀的时候再多态
        $payment = $this->getPayment($thirdPayData['t_pay_type']);
        list($data, $error) = $payment->createOrder($thirdPayData);
        if($error){
            $this->addError('', $error);
            return false;
        }
        return $data;
    }


    public function getPayment($type){
        switch ($type) {
            case Payment::ALIPAY:
                return new Alipay();
                break;
            case Payment::WXPAY:
                return new Wxpay();
                break;
            default:
                throw new \Exception('支付方式不支持'. $type);
                return false;
        }
    }

    public function validateData($notifyData, $type){
        $payment = $this->getPayment($type);
        return $payment->validateData($notifyData);
    }

    public function validatePayed($notifyData, $type){
        $payment = $this->getPayment($type);
        return $payment->validatePayed($notifyData);
    }

    public function getTradeNo($notifyData, $type){
        $payment = $this->getPayment($type);
        return $payment->getTradeNo($notifyData);
    }

    public function checkDataMatch($thirdOrder, $transData, $type){
        $payment = $this->getPayment($type);
        return $payment->checkDataMatch($thirdOrder, $transData);
    }







    public static function getDfPoValidDuration(){
        return 3600;
    }

    public static function checkPriceType($value){
        if(!is_int($value)){
            if(!preg_match('/^-?[0-9]+$/', $value)){
                return false;
            }else{
                $value = (int)$value;
            }
        }
        if($value > 2147483647 || $value < -2147493648){
            return false;
        }
        return true;
    }
}
