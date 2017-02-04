<?php
/**
 * 1. 定义一个接受格式
 * 2. 定义一个容错机制（发送不成功的消息不能消费掉）
 * 3. 计数
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';
use Workerman\Worker;
use PhpAmqpLib\Connection\AMQPStreamConnection;

$worker = new Worker("tcp://127.0.0.1:2345");
$worker::$logFile = (__DIR__) . '/run/ewlog.txt';
$worker::$pidFile = (__DIR__) . '/run/ewpid.txt';
$worker->count = 20;
$worker->onWorkerStart = function($worker)
{
    $transport  = new \Swift_SmtpTransport('smtp.qq.com', 465, 'ssl');
    // $transport->setUsername('kitral.zhong@trainor.cn');
    // $transport->setPassword('TDSZ2016kz');
    $transport->setUsername('784248377@qq.com');
    $transport->setPassword('qicai5619484');

    $data = [
        'subject' => null,
        'from' => null,
        'to' => null,
        'body' => null,
        'img' => [],
        'attatch' => []
    ];


    $connection = new AMQPStreamConnection('localhost', 5672, 'kitral', 'philips');
    $channel = $connection->channel();

    $channel->queue_declare('email-job', false, true, false, false);

    $callback = function($msg) use($transport, $worker){
        // 1. 得到数据
        // 2. 检查格式
        // 3. 构造对象
        // 4. 异常包括，发送
        // 5. 计数
        // 6. confirm ack
        // 7. finish

        // $mailer = \Swift_Mailer::newInstance($transport);
        // $emailMsg = \Swift_Message::newInstance('Wonderful Subject')
        //             ->setFrom(array('784248377@qq.com' => 'kz'))
        //             ->setTo(array('2957176853@qq.com'));
        // $emailMsg->setBody(  '<html>' .
        //                     ' <head></head>' .
        //                     ' <body>' .
        //                     '  Here is an image <img src="' . // Embed the file
        //                          $emailMsg->embed(\Swift_Image::fromPath('/home/kitral/Pictures/Wallpapers/1.jpg')) .
        //                        '" alt="Image" />' .
        //                     '  Rest of message' .
        //                     ' </body>' .
        //                     '</html>', 'text/html');
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        // $mailer->send($emailMsg);
        // echo "1\n";
    };

    $channel->basic_qos(null, 1, null);
    $channel->basic_consume('email-job', '', false, false, false, false, $callback);

    while(count($channel->callbacks)) {
        $channel->wait();
    }

    $channel->close();
    $connection->close();
};

// 运行所有Worker实例
Worker::runAll();
