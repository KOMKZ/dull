<?php
namespace backend\controllers;

use common\base\AdminController;

/**
 *
 */
class GoodsController extends AdminController
{
    public function actionList(){
        return $this->render('list');
    }

    public function actionAdd(){
        return $this->render('add');
    }

    public function actionUpdate(){
        return $this->render('update');
    }

    public function actionView(){
        return $this->render('view');
    }
}
