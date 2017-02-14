<?php
namespace common\base;

use Yii;
use common\base\Controller;


class AdminController extends Controller
{
    public $enableCsrfValidation = false;

    public function error($message){
        Yii::$app->session->setFlash('error', $message);
    }

    public function notfound(){
        $this->error(Yii::t('app', '数据不存在'));
    }

    public function succ($message){
        Yii::$app->session->setFlash('success', $message);
    }



}
