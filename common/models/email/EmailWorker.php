<?php
namespace common\models\email;

use Yii;
use common\models\email\tables\EmailFailed;

class EmailWorker
{
    CONST SEN_FIELD = 'worker:email:succ_email_num';

    static public $emailWorkerCount = 5;
    public $defaultSender = 'kitral.zhong@trainor.cn';
    public $defaultSenderPwd = 'TDSZ2016kz';
    static private $mailer;
    private $msg;
    private $source;

    private $subject;
    private $from;
    private $to;
    private $body;
    private $img = [];
    private $attatch = [];

    private $redis;
    private $db;



    public static function handleEmail($msg){
        $ew = new static();
        // 1. 加载数据
        $r = $ew->loadData($msg);
        if(!$r){
            $ew->insertFailedEmail($msg->body, $e->getCode(), $e->getMessage());
            return $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
        }
        try {
            // 2. 构造对象
            $ew->prepareEmail();
            // 3. 异常包括，发送
            $ew->send();
        } catch (\Exception $e) {
            $ew->insertFailedEmail($msg->body, $e->getCode(), $e->getMessage());
            return $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
        }
        // 4. 计数等成功处理
        $ew->handleSendSucc();
        // 5. confirm ack
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }
    private function send(){
        self::$mailer->send($this->msg);
    }

    private function insertFailedEmail($data, $code = null, $message = null){
        $table = EmailFailed::tableName();
        $sql = "
            insert into $table
            (`emf_id`, `emf_data`, `emf_code`, `emf_message`, `emf_created_at`)
            values
            (null, :p2, :p3, :p4, :p5)
        ";
        $sqlCommand = $this->getDb()->createCommand($sql, [
            ':p2' => is_string($data) ? $data : json_encode($json),
            ':p3' => $code,
            ':p4' => $message,
            ':p5' => time()
        ]);
        $sqlCommand->execute();
    }

    private function handleSendSucc(){
        $this->getRedis()->executeCommand('incr', [self::SEN_FIELD]);
    }

    private function prepareEmail(){
        // build transport
        $this->buildMailer($this->defaultSender, $this->defaultSenderPwd);
        $this->buildMsg();
    }
    private function buildMsg(){
        $this->from = $this->from ? $this->from : $this->defaultSender;
        $this->from = is_array($this->from) ? $this->from : [$this->from];
        $this->to = is_array($this->to) ? $this->to : [$this->to];
        // message instance
        $msg = \Swift_Message::newInstance($this->subject)
                    ->setFrom($this->from)
                    ->setTo($this->to);
        // build img attachment
        list($msgBody, $contentType) = $this->body;
        if(!empty($this->img) && 'text/html' == $contentType){
            $imgMap = [];
            foreach($this->img as $key => $imgPath){
                $id = '';
                if(file_exists($imgPath)){
                    $id = $msg->embed(\Swift_Image::fromPath($imgPath));
                }else{
                    // log
                    $id = '#';
                }
                $imgMap[$key] = $id;
            }
            $msgBody = strtr($msgBody, $imgMap);
        }
        // set msg body
        $msg->setBody($msgBody, $contentType);
        $this->msg = $msg;
    }
    private function buildMailer($sender, $pwd){
        if(self::$mailer){
            return self::$mailer;
        }
        $transport  = new \Swift_SmtpTransport('smtp.qq.com', 465, 'ssl');
        $transport->setUsername($sender);
        $transport->setPassword($pwd);
        return self::$mailer = \Swift_Mailer::newInstance($transport);
    }
    private function loadData($msg){
        $this->source = $msg;
        $data = json_decode($msg->body, true);
        if(false == $data){
            return false;
        }
        $safeNames = ['subject', 'from', 'to', 'body', 'img', 'attatch'];
        foreach($safeNames as $name){
            if(!empty($data[$name])){
                $this->$name = $data[$name];
            }
        }
        $require = ['subject', 'to', 'body'];
        foreach($require as $name){
            if(empty($this->$name)){
                return false;
            }
        }
        return true;
    }
    private function getRedis(){
        if($this->redis){
            return $this->redis;
        }
        return $this->redis = $this->newRedis();
    }

    private function newRedis(){
        try {
            return Yii::createObject([
                'class' => 'yii\redis\Connection',
                'hostname' => 'localhost',
                'port' => '6379',
                'database' => 0
            ]);
        } catch (\Exception $e) {
            // log
            return null;
        }
    }

    private function getDb(){
        if($this->db){
            return $this->db;
        }
        return $this->db = $this->newDb();
    }

    private function newDb(){
        try {
            return Yii::createObject([
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=localhost;dbname=dull',
                'username' => 'root',
                'password' => '123456',
                'charset' => 'utf8',
                'tablePrefix' => 'dull_'
            ]);
        } catch (\Exception $e) {
            // log
            return null;
        }
    }

}
