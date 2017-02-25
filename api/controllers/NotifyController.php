<?php
namespace api\controllers;

use Yii;
use common\base\ApiController;
use common\models\notify\NotifyModel;

/**
 *
 */
class NotifyController extends ApiController
{
    public function actionGetOneUserNotify($um_id){
        $notifyModel = new NotifyModel();
        $one = $notifyModel->getOneUserMsg(['um_id' => $um_id]);
        if(!$one){
            return $this->notfound();
        }
        return $this->succ($one->toArray());
    }
    public function actionSetNotifyRead($um_id){
        $notifyModel = new NotifyModel();
        $result = $notifyModel->setUserMsgRead(['um_id' => $um_id]);
        if(!$result){
            list($code, $error) = $notifyModel->getOneError();
            return $this->error($code, $error);
        }
        return $this->succ(null);

    }
}
