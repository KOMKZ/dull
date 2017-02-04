<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
/**
 *
 */
class DemoController extends Controller
{
    public function actionAddEmail(){
        set_time_limit(0);
        $connection = new AMQPStreamConnection('localhost', 5672, 'kitral', 'philips');
        $channel = $connection->channel();
        $channel->queue_declare('email-job', false, true, false, false);



        $msgBody = 'hello world'."\n";
        $i  = 1;
        while($i < 100){
            $msg = new AMQPMessage($msgBody, ['delivery_mode' => 2]);
            $channel->basic_publish($msg, '', 'email-job');
            $i++;
        }
        $channel->close();
        $connection->close();
    }
    public function actionSendEmail(){



    }
}
