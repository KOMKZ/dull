<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\web\UserException;

class SiteController extends AdminController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['error'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    // public function actions()
    // {
    //     return [
    //         'error' => [
    //             'class' => 'yii\web\ErrorAction',
    //         ],
    //     ];
    // }

    public function actionError()
    {
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            // action has been invoked not from error handler, but by direct route, so we display '404 Not Found'
            $exception = new HttpException(404, Yii::t('yii', 'Page not found.'));
        }

        if ($exception instanceof HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = $exception->getCode();
        }

        if ($exception instanceof \Exception) {
            $name = $exception->getName();
        } else {
            $name = Yii::t('yii', 'Error');
        }
        if ($code) {
            $name .= " (#$code)";
        }

        if ($exception instanceof UserException) {
            $message = $exception->getMessage();
        } else {
            $message = Yii::t('yii', 'An internal server error occurred.');
        }
        if (Yii::$app->getRequest()->getIsAjax()) {
            return "$name: $message";
        } else {
            if(Yii::$app->user->isGuest){
                $this->layout = 'main-login';
            }
            return $this->render('error' ?: $this->id, [
                'name' => $name,
                'message' => $message,
                'exception' => $exception,
            ]);
        }
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}
