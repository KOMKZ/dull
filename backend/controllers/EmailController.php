<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController;
use common\models\email\EmailModel;


class EmailController extends AdminController
{

    public function actionIndex()
    {
        $mailbox = Yii::$app->imap->connection;
        print_r($mailbox->searchMailBox(ALL));
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

    public function actionFailEmailView($id){
        $emailModel = new EmailModel();
        $one = $emailModel->getOneFailEmail(['emf_id' => $id]);
        return $this->render('fail-email-view', [
            'model' => $one
        ]);
    }

}
