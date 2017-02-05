<?php

/**
 * 1. 定义一个接受格式
 * 2. 定义一个容错机制（发送不成功的消息不能消费掉）
 * 3. 计数
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php';
require_once dirname(__DIR__) . '/common/config/bootstrap.php';
require_once __DIR__ . '/bootstrap.php';

use Workerman\Worker;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use \common\models\email\EmailWorker;
/**
 *
 */


$worker = new Worker("tcp://127.0.0.1:2345");
$worker::$logFile = (__DIR__) . '/run/ewlog.txt';
$worker::$pidFile = (__DIR__) . '/run/ewpid.txt';
$worker->count = EmailWorker::$emailWorkerCount;
$worker->onWorkerStart = function($worker)
{
    $connection = new AMQPStreamConnection('localhost', 5672, 'kitral', 'philips');
    $channel = $connection->channel();
    $channel->queue_declare('email-job', false, true, false, false);
    $channel->basic_qos(null, 1, null);
    $channel->basic_consume('email-job', '', false, false, false, false, ['\common\models\email\EmailWorker', 'handleEmail']);
    while(count($channel->callbacks)) {
        $channel->wait();
    }
    $channel->close();
    $connection->close();
};

// 运行所有Worker实例
Worker::runAll();
