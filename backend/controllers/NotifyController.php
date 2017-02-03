<?php
namespace backend\controllers;

use common\base\AdminController;

class NotifyController extends AdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSend(){
        return $this->render('send');
    }



}
