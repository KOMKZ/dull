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
    private $params;
    private $template;
    private $from;
    private $to;
    private $body;
    private $img = [];
    private $attatch = [];

    private static $redis;
    private static $db;



    public static function handleEmail($msg){
        $ew = new static();
        // 1. 加载数据
        $r = $ew->loadData($msg);
        if(!$r){
            $ew->insertFailedEmail($msg->body, $e->getCode(), $e->getMessage());
            return $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        }
        try {
            // 2. 构造对象
            $ew->prepareEmail();
            // 3. 异常包括，发送
            $ew->send();
        } catch (\Exception $e) {
            $ew->insertFailedEmail($msg->body, $e->getCode(), $e->getMessage());
            return $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        }
        // 4. 计数等成功处理
        $ew->handleSendSucc();
        // 5. confirm ack
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }
    private function send(){
        if(!self::$mailer->send()){
            throw new \Exception(self::$mailer->ErrorInfo);
        }
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
        // $this->from = is_array($this->from) ? $this->from : [$this->from];
        // $this->to = is_array($this->to) ? $this->to : [$this->to];
        self::$mailer->setFrom($this->from);
        self::$mailer->addAddress($this->to);
        self::$mailer->Subject = $this->subject;
        self::$mailer->isHTML(true);
        list($msgBody, $contentType) = $this->body;
        if(empty($msgBody) && $this->template){
            $tpl = $this->getTpl($this->template);
            $content = Yii::$app->view->renderFile($tpl['path'], $this->params);
            $msgBody = Yii::$app->view->renderFile($tpl['layout'], ['content' => $content]);
            $this->img = array_merge($this->img, $tpl['img']);
        }
        // 看看是不是需要嵌入图片
        if(!empty($this->img) && 'text/html' == $contentType){
            $imgMap = [];
            foreach($this->img as $key => $imgPath){
                $id = '';
                if(file_exists($imgPath)){
                    $id = self::$mailer->AddEmbeddedImage($imgPath, $key);
                }else{
                    // log
                    $id = '#';
                }
                $imgMap[$key] = $id;
            }
        }
        self::$mailer->Body = $msgBody;

        // // message instance
        // $msg = \Swift_Message::newInstance($this->subject)
        //             ->setFrom($this->from)
        //             ->setTo($this->to);
        // // build img attachment
        // list($msgBody, $contentType) = $this->body;
        // // set msg body
        // $msg->setBody($msgBody, $contentType);
        // $this->msg = $msg;
    }

    public static function getTpl($name = null){
        $map = [
            'signup-user-auth-email' => [
                'path' => Yii::getAlias('@common/mail/signup-user-auth-email.php'),
                'layout' => Yii::getAlias('@common/mail/layouts/html.php'),
                'img' => [
                    'tpl_img' => '/home/kitral/Pictures/2.jpg',
                ]
            ],
        ];
        return $name ? $map[$name] : $map;
    }
    private function buildMailer($sender, $pwd){
        if(self::$mailer){
            return self::$mailer;
        }
        // swiftmailer
        // $transport  = new \Swift_SmtpTransport('smtp.qq.com', 465, 'ssl');
        // $transport->setUsername($sender);
        // $transport->setPassword($pwd);
        // return self::$mailer = \Swift_Mailer::newInstance($transport);

        // php mailer
        $mail = new \PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.qq.com';
        $mail->SMTPAuth = true;
        $mail->Username = $sender;
        $mail->Password = $pwd;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        return self::$mailer = $mail;
    }
    private function loadData($msg){
        $this->source = $msg;
        $data = json_decode($msg->body, true);
        if(false == $data){
            return false;
        }
        $safeNames = ['subject', 'from', 'to', 'body', 'img', 'attatch', 'template', 'params'];
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
        if(self::$redis){
            return self::$redis;
        }
        return self::$redis = $this->newRedis();
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
        if(self::$db){
            return self::$db;
        }
        return self::$db = $this->newDb();
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
