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
        return $this->render('list');
    }

    public function actionGetOne($id){
        $userModel = new UserModel();
        $one = $userModel->getOne(['id' => $id]);
        return $this->render('add');
    }

    public function actionAdd(){
        $userModel = new UserModel();
        $user = new User();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $result = $userModel->createUser($post, $user);
            if($result){
                console($result->toArray());
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
