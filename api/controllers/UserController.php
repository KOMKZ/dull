<?php
namespace api\controllers;

use Yii;
use common\base\ApiController;
use common\models\user\UserModel;

/**
 *
 */
class UserController extends ApiController
{
    public function actionSetStatus(){
        $userModel = new UserModel();
        $post = Yii::$app->request->getBodyParams();
        if(empty($post['u_id'])){
            return $this->error('', Yii::t('app', '缺少u_id'));
        }
        if(empty($post['u_status'])){
            return $this->error('', Yii::t('app', '缺少u_status'));
        }
        if(is_array($post['u_id'])){
            $condition = ['in', 'u_id', $post['u_id']];
        }else{
            $condition = ['u_id' => $post['u_id']];
        }
        $result = $userModel->updateAllStatus($condition, $post['u_status']);
        if(!$result){
            return $this->error('', $userModel->getErrors());
        }
        return $this->succ(null, Yii::t('app', '操作成功'));
    }
}
