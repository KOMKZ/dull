<?php
namespace backend\controllers;

use common\base\AdminController;

class SiteController extends AdminController
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}
