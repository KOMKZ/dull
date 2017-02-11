<?php
namespace common\models\email;

use yii\base\Object;


/**
 *
 */
class Mail extends Object
{
    public $subject;
    public $params;
    public $template;
    public $from;
    public $to;
    public $body;
    public $img = [];
    public $attatch = [];

    public function prepare(){
        
    }

    private function buildMailer($sender, $pwd){
        if(self::$mailer){
            return self::$mailer;
        }
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
}
