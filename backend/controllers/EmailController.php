<?php
namespace backend\controllers;

use common\base\AdminController;

class EmailController extends AdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSend(){
        return $this->render('send');
    }

}
