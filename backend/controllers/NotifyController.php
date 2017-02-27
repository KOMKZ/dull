<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController;
use common\models\notify\tables\SysMsg;
use common\models\notify\tables\UserMsg;
use common\models\notify\NotifyModel;
use common\models\user\UserModel;
use yii\helpers\Url;

class NotifyController extends AdminController
{

    public function actionIndex()
    {
        $getData = Yii::$app->request->get();
        $notifyModel = new NotifyModel();


        $condition['um_uid'] = Yii::$app->user->getId();
        if(isset($getData['um_read_status']) && in_array($getData['um_read_status'], ['1,0', '1', '0'])){
            $condition['um_read_status'] = $getData['um_read_status'];
        }


        list($provider, $pagination) = $notifyModel->getUserMsgProvider($condition);

        return $this->render('index', [
            'provider' => $provider,
            'readStatusMap' => UserMsg::getValidConsts('um_read_status'),
            'getOneNotifyUrl' => Yii::$app->apiurl->createAbsoluteUrl(['notify/get-one-user-notify']),
            'setNotifyReadUrl' => Yii::$app->apiurl->createAbsoluteUrl(['notify/set-notify-read']),
            'searchNotifyUrl' => Url::toRoute(['notify/index']),
            'pullNotifyUrl' => Yii::$app->apiurl->createAbsoluteUrl(['notify/pull'])
        ]);
    }

    public function actionSend(){
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $notifyModel = new NotifyModel();
            $result = $notifyModel->createMsg($post['SysMsg']);
            if(!$result){
                list($code, $error) = $notifyModel->getOneError();
                $this->error($code, $error);
                return $this->refresh();
            }else{
                $this->succ();
                return $this->refresh();
            }
        }
        $sysMsg = new SysMsg();
        return $this->render('send', [
            'model' => $sysMsg,
            'mTplTypeMap' => NotifyModel::getMTplTypeMap(true),
            'mTplTypeData' => NotifyModel::getMTplTypeMap(),
            'mUseTplMap' => SysMsg::getValidConsts('sm_use_tpl'),
            'mSmObjectTypeMap' => SysMsg::getValidConsts('sm_object_type')
        ]);
    }



}
