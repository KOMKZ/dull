<?php
namespace common\models\notify;

use Yii;
use common\base\Model;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use common\models\notify\tables\SysMsg;
use common\models\notify\tables\UserMsg;
use common\models\user\UserModel;

/**
 *
 */
class NotifyModel extends Model
{
    public function getOneUserMsg($condition){
        if(is_object($condition)){
            return $condition;
        }
        if($condition){
            return UserMsg::find()->where($condition)->one();
        }else{
            return null;
        }
    }

    public function setUserMsgRead($condition){
        $msg = $this->getOneUserMsg($condition);
        if(!$msg){
            return $this->addError('', '数据不存在');
        }
        $msg->um_read_status = 1;
        if(false === $msg->update(False)){
            $this->addError('', '更新失败');
            return false;
        }
        return $msg;
    }

    public function pullUserMsg($uid, $save = true){
        $userModel = new UserModel();
        $user = $userModel->getOne(['u_id' => $uid]);
        if(!$user){
            return [];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $query = $this->getUserNewMsgQuery($uid);
            $newMsg = $query->asArray()->all();
            if($newMsg){
                $data = [];
                foreach($newMsg as $item){
                    $data[] = [
                        'um_uid' => $uid,
                        'um_mid' => $item['sm_id'],
                        'um_read_status' => 0,
                        'um_title' => $item['sm_title'],
                        'um_content' => $this->buildMsgContent($item['sm_content'], $this->buildMsgParams($user)),
                        'um_created_at' => $item['sm_created_at']
                    ];
                }
                if($save){
                    $sqlCommand = Yii::$app->db->createCommand()->batchInsert(UserMsg::tableName(), array_keys($data[0]), $data);
                    $sqlCommand->execute();
                }
                $transaction->commit();
                return $data;
            }else{
                return [];
            }
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    protected function buildMsgParams($user){
        return [
            '{username}' => $user->u_username
        ];
    }

    protected function buildMsgContent($tplContent, $params = []){
        return strtr($tplContent, $params);
    }

    protected function getUserNewMsgQuery($uid){
        $sysMsgTable = SysMsg::tableName();
        $userMsgTable = UserMsg::tableName();
        $sql = "
        select
            s.*, um.*
        from
            $sysMsgTable as s
        left join
            $userMsgTable as um
        on
            s.sm_id = um.um_mid
            and
            um.um_uid = :uid
        where
            s.sm_expired_at >= :time
            and
            s.sm_object_type = :global_type
            and
            um.um_id is null
        ";
        $params = [
            ':time' => time(),
            ':global_type' => SysMsg::GLOBAL_MSG,
            ':uid' => $uid
        ];
        $query = SysMsg::findBySql($sql, $params);
        return $query;
    }

    public static function getLatestUserMsg($uid, $num = 5, $status = '1,0'){
        $status = explode(',', $status);
        return UserMsg::find()
                        ->where(['um_uid' => $uid, 'um_read_status' => $status])
                        ->limit($num)
                        ->orderBy(['um_created_at' => SORT_DESC])
                        ->asArray()
                        ->all();
    }

    public function getUserMsgProvider($condition = [], $sortData = [], $withPage = true){
        $query = UserMsg::find();
        $query = $this->buildQueryWithCondition($query, $condition);

        $defaultOrder = [
            'um_created_at' => SORT_DESC
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
                'attributes' => ['um_created_at'],
                'defaultOrder' => $defaultOrder
            ]
        ]);

        $pagination = $provider->getPagination();
        return [$provider, $pagination];
    }

    public function getProvider($condition = [], $sortData = [], $withPage = true){
        $query = SysMsg::find();
        $query = $this->buildQueryWithCondition($query, $condition);
        $query->with('create_user');
        $defaultOrder = [
            'sm_created_at' => SORT_DESC
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
                'attributes' => ['sm_created_at'],
                'defaultOrder' => $defaultOrder
            ]
        ]);

        $pagination = $provider->getPagination();
        return [$provider, $pagination];
    }

    public static function getMTplTypeMap($noContent = false){
        $map = require(dirname(__FILE__) . '/' . 'm-tpl-type-map.php');
        if(!$noContent){
            return $map;
        }else{
            return ArrayHelper::map($map, 'value', 'label');
        }

    }
    public function createMsg($data, $params = []){
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $userModel = new UserModel();
            $sysMsg = new SysMsg();
            $sysMsg->scenario = 'create';
            if(!$sysMsg->load($data, '') || !$sysMsg->validate()){
                $this->addError('', $this->getArErrMsg($sysMsg));
                return false;
            }
            // build title and content
            if($sysMsg->isUseTpl){
                $tplMap = self::getMTplTypeMap();
                $tplData = $tplMap[$sysMsg->sm_tpl_type];
                $sysMsg->sm_title = $tplData['title'];
                $sysMsg->sm_content = $tplData['content'];
            }elseif(empty($sysMsg->sm_title) || empty($sysMsg->sm_content)){
                $this->addError('', 'sm_title 或者 sm_content 不能为空');
                return false;
            }
            $sysMsg->sm_content = $this->buildMsgContent($sysMsg->sm_content, $params);

            // send user id
            $sysMsg->sm_create_uid = Yii::$app->user->getId();

            if($sysMsg->isGlobalMsg){
                $sysMsg->sm_object_id = 0;
            }elseif(empty($sysMsg->sm_object_id)){
                $this->addError('', '必须填写接收人');
                return false;
            }elseif(!($targetUser = $userModel->getOne(['u_id' => $sysMsg->sm_object_id]))){
                $this->addError('', '接收人不存在');
                return false;
            }
            if(!$sysMsg->isPrivateMsg){
                // insert record to sys_msg table
                $result = $sysMsg->insert(false);
                if(!$result){
                    $this->addError('', Yii::t('app', '插入失败'));
                    return false;
                }
                $transaction->commit();
                return $sysMsg;
            }else{
                // insert record to user_msg table for private msg
                $userMsg = new UserMsg();
                $userMsg->um_uid = $sysMsg->sm_object_id;
                $userMsg->um_title = $sysMsg->sm_title;
                $userMsg->um_content = $this->buildMsgContent($sysMsg->sm_content, $this->buildMsgParams($targetUser));
                $userMsg->um_read_status = 0;
                $userMsg->um_created_at = time();
                $result = $userMsg->insert(false);
                if(!$result){
                    $this->addError('', Yii::t('app', '插入失败'));
                    return false;
                }

                $transaction->commit();
                return $userMsg;
            }
        } catch (\Exception $e) {
            Yii::error($e);
            $transaction->rollback();
            $this->addError('', $e->getMessage());
            return false;
        }
    }


}
