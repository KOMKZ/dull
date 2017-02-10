<?php
namespace backend\controllers;

use common\base\AdminController;
use common\models\email\EmailModel;


class EmailController extends AdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSend(){
        return $this->render('send');
    }

    public function actionFailEmailList(){
        $emailModel = new EmailModel();
        list($provider, $pagination) = $emailModel->getFailedEmailProvider();
        return $this->render('fail-email-list', [
            'provider' => $provider
        ]);
    }

}
