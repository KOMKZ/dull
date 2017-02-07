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
    public function actionLog(){
        Yii::error(mt_rand(111111111111,99999999999999999999));
    }
    public function actionAddEmail(){
        set_time_limit(0);
        $emailModel = new EmailModel();
        $msgBody = [
            'subject' => '测试邮件',
            'to' => '784248377@qq.com',
            'body' => [
                      '<html>' .
                      ' <head></head>' .
                      ' <body>' .
                      '  Here is an image <img src="cid:%img1%" alt="Image" />' .
                      '  Rest of message' .
                      ' </body>' .
                      '</html>',
                      'text/html'
            ],
            'img' => [
                '%img1%' => '/home/kitral/Pictures/Wallpapers/1.jpg',
            ],
        ];
        $i  = 1;
        while($i < 200){
            $emailModel->sendEmail($msgBody);
            $i++;
        }
    }
    public function actionSendEmail(){



    }
}
