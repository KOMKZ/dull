<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController;
use common\models\user\UserModel;
use common\models\user\tables\User;
use yii\filters\AccessControl;
use common\models\user\GroupModel;
use common\models\rbac\RbacModel;

class UserController extends AdminController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }



    public function actionLogin(){
        $user = new User();
        $userModel = new UserModel();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $condition = [];
            if(!empty($post['login_id'])){
                $condition['u_username'] = $post['login_id'];
            }
            $result = $userModel->login($condition, $post['password'], empty($post['remember']) ? false : $post['remember']);
            if($result){

                return $this->redirect(['site/index']);
            }else{
                list($code, $error) = $userModel->getOneError();
                Yii::$app->session->setFlash('error', $error . ':' . $code);
                return $this->refresh();
            }
        }
        return $this->render('login', [
            'model' => $user
        ]);
    }

    public function actionLogout(){
        if(Yii::$app->request->isPost && !Yii::$app->user->isGuest){
            Yii::$app->user->logout();
            return $this->refresh();
        }
        return $this->home();
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGroupList(){
        $groupModel = new GroupModel();
        list($provider, $pagination) = $groupModel->getProvider();
        return $this->render('group-list', [
            'provider' => $provider
        ]);
    }

    public function actionGroupView($name){
        $groupModel = new GroupModel();
        $one = $groupModel->getOne(['ug_name' => $name]);
        if(!$one){
            return $this->error(Yii::t('app', '数据不存在'));
        }
        return $this->render('group-view', [
            'model' => $one
        ]);
    }

    public function actionGroupUpdate($name){
        $groupModel = new GroupModel();
        $rbacModel = new RbacModel();
        $one = $groupModel->getOne(['ug_name' => $name]);
        if(!$one){
            return $this->error(Yii::t('app', '数据不存在'));
        }
        return $this->render('group-update', [
            'model' => $one,
            'validRoles' => $rbacModel->getRoles(),
            'assignedRoles' => $rbacModel->getRolesByAi($one['ug_name']),
            'groupPmiAdminUrl' => Yii::$app->apiurl->createAbsoluteUrl(['rbac/assign-admin']),
            'delGroupUrl' => '#'
        ]);
    }

    public function actionList(){
        $userModel = new UserModel();
        list($provider, $pagination) = $userModel->getProvider();

        return $this->render('list', [
            'provider' => $provider,
            'userAuthStatusMap' => User::getValidConsts('u_auth_status'),
            'userStatusMap' => User::getValidConsts('u_status'),
            'userSetStatusApi' => Yii::$app->apiurl->createAbsoluteUrl(['user/set-status'], 'http'),
        ]);
    }

    public function actionUpdate($u_id){
        $userModel = new UserModel();
        $one = $userModel->getOne(['u_id' => $u_id]);
        if(!$one){
            return $this->error(Yii::t('app', '数据不存在'));
        }
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $result = $userModel->updateUser($one, $post);
            if($result){
                $this->succ(Yii::t('app', '操作成功'));
                return $this->refresh();
            }
        }
        return $this->render('update', [
            'model' => $one,
            'userStatusMap' => User::getValidConsts('u_status'),
            'userAuthStatusMap' => User::getValidConsts('u_auth_status'),
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
                $this->succ(Yii::t('app', '操作成功'));
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
