<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController;
use common\models\user\UserModel;
use common\models\user\tables\User;

class UserController extends AdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList(){
        $userModel = new UserModel();
        list($provider, $pagination) = $userModel->getProvider();

        return $this->render('list', [
            'provider' => $provider,
            'userAuthStatusMap' => User::getValidConsts('u_auth_status')
        ]);
    }


    public function actionView($u_id){
        $userModel = new UserModel();
        $one = $userModel->getOne(['u_id' => $u_id]);
        if(!$one){
            return $this->error(Yii::t('app', '数据不存在'));
        }
        return $this->render('view', [
            'model' => $one,
            'userStatusMap' => User::getValidConsts('u_status'),
            'userAuthStatusMap' => User::getValidConsts('u_auth_status'),
        ]);
    }

    public function actionAdd(){
        $userModel = new UserModel();
        $user = new User();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $result = $userModel->createUser($post, $user);
            if($result){
                // todo 提示
                return $this->refresh();
            }
        }
        return $this->render('add', [
            'model' => $user,
            'userStatusMap' => User::getValidConsts('u_status'),
            'userAuthStatusMap' => User::getValidConsts('u_auth_status'),
        ]);
    }


}
