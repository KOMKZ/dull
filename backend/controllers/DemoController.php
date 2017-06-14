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
use common\models\log\ActionModel;

/**
 *
 */
class DemoController extends Controller
{
    public $enableCsrfValidation = false;
    public $c = null;

    public function action22(){

    }

    public function action1(){
        console($this->c()->indices()->delete(['index' => 'multiple02']));
    }
    public function actionGet(){
        $str = \Yii::$app->request->get('q');
        $params = $this->getParams($str);
        $r = $this->c('local')->search($params);
        $data = [];
        foreach($r['hits']['hits'] as $words){
            $data[] = $words;
        }
        $a = sprintf("<p>total：%s, query: %s, took:%sms</p>", $r['hits']['total'], $str, $r['took']);
        foreach($data as $p){
            $highlight = "";
            if(!empty($p['highlight'])){
                foreach($p['highlight'] as $name => $hc){
                    $highlight .= sprintf("<p class='sub'><span class='green'>%s</span>:%s <span class='grey'>%s</span></p>", $p['_score'], implode(',', $hc), $p['_source']['type']);
                }
            }
            $a .= sprintf("
            <p>%s</p>
            ",
            $highlight
        );
        }
        echo $a;
        exit();

    }
    public function getParams($str){
        $params = [
            'index' => 'keyword02',
            'type' => 'keyword',
            'size' => '30',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['match' => ['value' => ['query' => $str, 'minimum_should_match' => '65%']]],
                        ],
                        'should' => [
                            ['match' => ['type' => ['query' => 'hse_product', 'boost' => 5]]],
                            ['match' => ['type' => ['query' => 'hse_keyword', 'boost' => 4]]],
                            ['match' => ['type' => ['query' => 'hse_keyword_baidu', 'boost' => 2]]],
                            [
                                'bool' => [
                                    'must' => [
                                        ['match' => ['type' => ['query' => 'hse_user_realname']]],
                                        ['match' => ['value' => ['query' => $str, 'type' => 'phrase','boost' => 8]]],
                                    ]
                                ]
                            ],
                            [
                                'bool' => [
                                    'must' => [
                                        ['match' => ['value.smart_value' => ['query' => $str, 'boost' => 4]]],
                                        [
                                            'bool' => [
                                                'must_not' => [
                                                    'match' => ['type' => ['query' => 'common_dict']]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'highlight' => [
                    "pre_tags" => ["<em>"],
                    "post_tags" => ["</em>"],
                    'fields' => [
                        'value' => new \StdClass(),
                        'value.smart_value' => new \StdClass()
                    ]
                ]
            ]
        ];
        return $params;
    }
    public function actionKw(){
        $str = \Yii::$app->request->get('q');
        $str = $str ? $str : "";
        $params = $this->getParams($str);
        $r = $this->c('local')->search($params);
        // console($r['hits']['hits'][0]);
        $data = [];
        foreach($r['hits']['hits'] as $words){
            $data[] = $words;
        }
        return $this->renderPartial('kw', [
            'data' => $data,
            'total' => $r['hits']['total'],
            'query' => $str,
            'took' => $r['took']
        ]);
    }
    public function action3(){
        $params = [
            'index' => 'multiple02',
            // 'analyzer' => 'cn_words',
            'field' => 'title',
            'body' => [
                'text' => '防爆电器'
            ]
        ];
        console($this->c()->indices()->analyze($params));

    }
    public function actionIndex(){
        $str = \Yii::$app->request->get('q');
        $str = $str ? $str : '';
        $params = [
            'index' => 'multiple02',
            'type' => 'product',
            'size' => 1000,
            'analyzer' => 'cn_words',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'multi_match' => [
                                    'query' => $str,
                                    'fields' => [
                                        'title^4', 'long_words_title^5',
                                        'industry', 'industry.long_word_industry^3',
                                        'keyword', 'long_word_keyword^3',
                                        'classification', 'long_word_classification^3',
                                        'content^2', 'long_words_content^4'
                                    ],
                                    'minimum_should_match' => '60%',
                                    'type' => 'cross_fields'
                                ]
                            ],
                        ]
                    ]
                ],
                'highlight' => [
                    "pre_tags" => ["<em>"],
                    "post_tags" => ["</em>"],
                    'fields' => [
                        'title' => new \StdClass(),
                        'industry' => new \StdClass(),
                        'keyword' => new \StdClass(),
                        'classification' => new \StdClass(),
                        'content' => new \StdClass()
                    ]
                ]
            ]
        ];
        $r = $this->c()->search($params);
        // console($r['hits']['hits'][0]);
        $data = [];
        foreach($r['hits']['hits'] as $product){
            $data[] = $product;
        }
        return $this->renderPartial('index', [
            'data' => $data,
            'total' => $r['hits']['total'],
            'query' => $str,
            'took' => $r['took']

        ]);
    }





    public function c($h = ''){
        $h = $h == 'local' ? 'localhost' : '192.168.1.45';
        if($this->c){
            return $this->c;
        }
        $hosts = [
                    "{$h}:9200",
                ];
        return $this->c = ClientBuilder::create()           // Instantiate a new ClientBuilder
                                    ->setHosts($hosts)      // Set the hosts
                                    ->build();;
    }

}
