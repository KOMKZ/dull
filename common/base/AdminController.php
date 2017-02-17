<?php
namespace common\base;

use Yii;
use common\base\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class AdminController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['root_role']
                    ]
                ]
            ]
        ];
    }

    public $enableCsrfValidation = false;

    public function error($message, $code){
        Yii::$app->session->setFlash('error', $message . ':' . $code);
    }

    public function notfound(){
        $this->error(Yii::t('app', '数据不存在'));
    }

    public function succ($message){
        Yii::$app->session->setFlash('success', $message);
    }



}
