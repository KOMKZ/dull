<?php
namespace backend\controllers;

use common\base\AdminController;

class SettingController extends AdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

}
