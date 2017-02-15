<?php
namespace backend\controllers;

use common\base\AdminController;

/**
 *
 */
class CaptchaController extends AdminController
{
    public function behaviors(){
        return [];
    }

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                // 'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
}
