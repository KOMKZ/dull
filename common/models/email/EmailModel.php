<?php
namespace common\models\email;

use Yii;
use common\base\Model;
use common\models\email\tables\EmailFailed;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


/**
 *
 */
class EmailModel extends Model
{
    static private $amqpConn;
    static private $Channel;
    
    public function sendEmail($data, $asyc = true){
        if($asyc && $this->isAsycSendOk()){
            return $this->sendEmailAsyc($data);
        }else{
            return $this->sendEmailSyc($data);
        }
    }
    protected function isAsycSendOk(){
        return true;
    }
    protected function sendEmailAsyc($data){
        $connection = $this->getAmqpConn();
        $channel = $this->getChannel();
        $msg = new AMQPMessage(json_encode($data), ['delivery_mode' => 2]);
        $channel->basic_publish($msg, '', 'email-job');
    }

    /**
     * [sendEmailSyc description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    protected function sendEmailSyc($data){

    }
    private function getChannel(){
        if(self::$Channel){
            return self::$Channel;
        }
        $conn = $this->getAmqpConn();
        $channel = $conn->channel();
        $channel->queue_declare('email-job', false, true, false, false);
        return self::$Channel = $channel;
    }

    private function getAmqpConn(){
        if(self::$amqpConn){
            return self::$amqpConn;
        }
        return self::$amqpConn = new AMQPStreamConnection('localhost', 5672, 'kitral', 'philips');
    }
}
