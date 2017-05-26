<?php
namespace common\models\order;

use Yii;
use common\base\Model;
use common\models\order\tables\Trans;
use common\models\allocate\AllocateModel;
use yii\data\ActiveDataProvider;

/**
 *
 */
class TransModel extends Model
{
    public function getOne($condition){
        if(is_object($condition)){
            return $condition;
        }
        if($condition){
            return Trans::find()->where($condition)->one();
        }else{
            return null;
        }
    }
    public function updateTransPayed($condition, $appData){
        $trans = $this->getOne($condition);
        if(!$trans){
            $this->addError('', '数据不存在');
            return false;
        }
        // 修改交易主数据
        $trans->t_pay_status = Trans::TPS_PAYED;
        $trans->t_payed_time = time();
        $trans->t_status = Trans::TS_COMPLETED;
        $trans->t_succ_pay_type = $appData['t_succ_pay_type'];
        if(false === $trans->update(false)){
            $this->addError('', '修改交易支付成功失败');
            return false;
        }
        // 修改支付单数据
        

    }
    public function getProvider($condition = [], $sortData = [], $withPage = true){
        $query = Trans::find();
        $query = $this->buildQueryWithCondition($query, $condition);

        $defaultOrder = [
            't_created_at' => SORT_DESC
        ];

        if(!empty($sortData)){
            $defaultOrder = $sortData;
        }
        $pageConfig = [];
        if(!$withPage){
            $pageConfig['pageSize'] = 0;
        }else{
            $pageConfig['pageSize'] = 10;
        }
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pageConfig,
            'sort' => [
                'attributes' => ['t_created_at'],
                'defaultOrder' => $defaultOrder
            ]
        ]);
        $pagination = $provider->getPagination();
        return [$provider, $pagination];
    }

    /**
     * @param  [type] $transData [description]
     * 't_title' => 'text transaction', // 交易的标题 ，必填
     * 't_type' => Trans::TYPE_CONSUME, // 交易的类型，必填 @see Trans::getValidConsts('t_type')
     * 't_fee_type' => Payment::TYPE_CNY, // 交易的货币类型，可选 Payment::getValidConsts('p_type')
     * 't_fee' => 1, // 交易的资金，必填
     * 't_pa_uid' => 1, // 交易的甲方，必填
     * 't_pb_uid' => 2, // 交易的乙方，必填
     * 't_des' => '', // 交易的描述，可选
     * 't_out_trade_no' => '111111888888', // 交易的应用号，必填
     * 't_out_trade_type' => '1', // 交易的应用类型，可选
     * 't_app_id' => '1', // 交易的应用id，可选
     * @return [type]           [description]
     */
    public function createTrans($transData){
        $t = Yii::$app->db->beginTransaction();
        try {
            $trans = new Trans();
            $trans->scenario = 'create';
            if(!$trans->load($transData, '') || !$trans->validate()){
                $this->addError('', $this->getArErrMsg($trans));
                return false;
            }
            $trans->t_number = AllocateModel::applyTransNumber();
            $trans->t_closed_time = 0;
            $trans->t_payed_time = 0;
            $trans->t_status = Trans::TS_INIT;
            $trans->t_pay_status = Trans::TPS_NOT_PAY;
            $trans->t_invalid_after = time() + 1;
            if(!$trans->insert(false)){
                $this->addError('', '插入失败');
                return false;
            }
            $t->commit();
            return $trans;
        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError('', '创建交易发生异常');
            return false;
        }

    }
    public static function getDfTrsValidDuration(){
        return 1;
    }

}
