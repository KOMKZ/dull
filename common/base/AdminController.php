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

    public function succ($message){
        Yii::$app->session->setFlash('success', $message);
    }

    

}
