<?php
namespace frontend\controllers;

use Yii;
use common\base\FrController;

class SiteController extends FrController
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

    public function actionAddLog(){
        Yii::error('log test');

    }

}
