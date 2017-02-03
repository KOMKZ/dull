<?php
namespace backend\controllers;

use common\base\AdminController;

class MonitorController extends AdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }
    
}
