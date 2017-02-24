<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use common\models\email\EmailModel;
/**
 *
 */
class DemoController extends Controller
{
    public function actionIndex(){
        return $this->render('index');
    }
    public function actionLog(){
        Yii::error(mt_rand(111111111111,99999999999999999999));
    }
    public function actionAddEmail(){
        set_time_limit(0);
        $emailModel = new EmailModel();
        $mail = [
            'subject' => '测试邮件',
            'to' => '784248377@qq.com',
            'template' => 'signup-user-auth-email',
            'body' => ['','text/html'],
            'img' => [
                'img01' => '/home/kitral/Pictures/04.png',
            ],
            'params' => [
                'username' => '784248377@qq.com',
                'auth_url' => 'http://localhost/helloworld'
            ]
        ];
        $i  = 1;
        while($i < 2){
            $emailModel->sendEmail($mail);
            $i++;
        }
    }
    public function actionSendEmail(){



    }
}
