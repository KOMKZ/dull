<?php
namespace backend\controllers;

use common\base\AdminController;

class PostController extends AdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList(){
        return $this->render('list');
    }

}
