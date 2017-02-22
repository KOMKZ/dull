<?php
namespace api\controllers;

use Yii;
use common\base\ApiController;
use yii\web\HttpException;
use yii\web\UserException;

class SiteController extends ApiController
{
    public function actionError(){
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
            $name = 'Error';
        }
        if ($code) {
            $name .= " (#$code)";
        }
        if ($exception instanceof UserException) {
            $message = $exception->getMessage();
        } else {
            $message = 'An internal server error occurred.';
        }
        return $this->error($code, $message);
    }

    public function actionIndex()
    {
    }


}
