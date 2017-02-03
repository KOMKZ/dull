<?php
namespace backend\controllers;

use common\base\AdminController;

class RbacController extends AdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }


}
