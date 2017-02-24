<?php
namespace common\base;

use Yii;
use common\base\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class AdminController extends Controller
{
    public function behaviors()
    {
        return YII_ENV != 'dev' ?[
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        // 'roles' => [$this->route]
                        'roles' => ['@']
                    ]
                ]
            ]
        ] : [];
    }

    public $enableCsrfValidation = false;

    public function error($code, $message){
        Yii::$app->session->setFlash('error', $message . ':' . $code);
    }

    public function notfound(){
        throw new NotFoundHttpException();
    }

    public function succ($message = null){
        Yii::$app->session->setFlash('success', $message ? $message : '成功');
    }



}
