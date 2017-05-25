<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
// use common\base\AdminController as Controller;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use common\models\email\EmailModel;
use alipay\AliPayment;
use yii\helpers\ArrayHelper;
use common\models\user\UserModel;
use common\models\notify\NotifyModel;
use common\models\notify\tables\SysMsg;
use yii\helpers\Url;
use common\models\file\FileModel;
use common\models\post\PostModel;

use Elasticsearch\ClientBuilder;

/**
 *
 */
class DemoController extends Controller
{
    public $enableCsrfValidation = false;
    public $c = null;


    public function actionIndex(){
        $params = [
            'index' => 'db',
            'type' => 'post',
            'id' => 2,
            'body' => [
                'title' => 'Quick brown rabbits',
                'body' => 'Brown rabbits are commonly seen.'
            ]
        ];
        $r = $this->c()->index($params);
        console($r);
    }

    public function action1(){
        $params = [
            'index' => 'db',
            'type' => 'post',
            'body' => [
                'query' => [
                    'dis_max' => [
                        'queries' => [
                            ['match' => ['title' => 'Quick Pets']],
                            ['match' => ['body' => 'Quick Pets']]
                        ]
                    ]
                ]
            ]
        ];
        $r = $this->c()->search($params);
        console($r);
    }




    public function c(){
        if($this->c){
            return $this->c;
        }
        $hosts = [
                    "localhost:9200",
                ];
        return $this->c = ClientBuilder::create()           // Instantiate a new ClientBuilder
                                    ->setHosts($hosts)      // Set the hosts
                                    ->build();;
    }

}
