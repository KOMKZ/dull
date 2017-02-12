<?php
namespace frontend\controllers;

use Yii;
use common\base\FrController;
use common\models\user\UserModel;
use common\models\user\tables\User;

/**
 *
 */
class UserController extends FrController
{
    public function actionSignupAuth($id, $token, $expire, $sign){
        $userModel = new UserModel();
        $isAuthed = $userModel->validateSignUpAuthData($id, $token, $expire, $sign);
        if(!$isAuthed){
            console($userModel->getErrors());
        }
        $result = $userModel->updateUserAuthed(['u_id' => $id]);
        if(!$result){
            console($userModel->getErrors());
        }else{
            console('注册验证成功');
        }



    }


}
