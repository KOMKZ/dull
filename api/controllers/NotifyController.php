<?php
namespace api\controllers;

use Yii;
use common\base\ApiController;
use common\models\notify\NotifyModel;
use common\models\user\UserModel;


/**
 *
 */
class NotifyController extends ApiController
{
    public function actionPull($u_username = ''){
        $post = Yii::$app->request->post();
        if(!empty($post['u_username'])){
            $u_username = $post['u_username'];
            $userModel = new UserModel();
            $user = $userModel->getOne(['u_username' => $u_username]);
            if(!$user){
                return $this->notfound("指定的用户不存在{$u_username}");
            }
            $notifyModel = new NotifyModel();
            $result = $notifyModel->pullUserMsg($user->u_id);
            return $this->succ($result);
        }
        return $this->succ([]);
    }
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
