<?php
namespace backend\controllers;

use common\base\AdminController;

class UserController extends AdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList(){
        return $this->render('list');
    }

    public function actionAdd(){
        return $this->render('add');
    }

}
