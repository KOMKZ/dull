<?php
namespace backend\controllers;

use common\base\AdminController;

class LogController extends AdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSearch(){
        return $this->render('search');
    }

}
