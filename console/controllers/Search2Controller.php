<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use Elasticsearch\ClientBuilder;

/**
"_id":18,
"title":"GB50316-2000工业金属管道设计规范",
"content":null,
"uid":1,
"industry":null,
"cls_id":22,
"keyword":"",
"format_id":5,
"approved":"0",
"is_delete":"0",
"create_time":1486566763,
"update_time":1486566763,
"classification":"国际规范和标准"
 */


class Search2Controller extends Controller{
    public function action2(){
        $dictFile = '/home/kitral/shuguang/dull/console/controllers/dict/hse_product.dict';
        $baiduFile =  '/home/kitral/shuguang/dull/console/controllers/dict/hse_product_baidu.dict';
        $handle = @fopen($dictFile, "r");
        $cmd = "
curl 'https://www.baidu.com/s?wd=%s&tn=baidurs2top' -H 'Pragma: no-cache' -H 'Accept-Encoding: gzip, deflate, sdch' -H 'Accept-Language: zh-CN,zh;q=0.8' -H 'Upgrade-Insecure-Requests: 1' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.109 Safari/537.36' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8' -H 'Cache-Control: no-cache' -H 'Cookie: BDUSS=DE0LXZpbXROZEMxNzEtTFA3MExDY3FaUXZOZTJVfm5xeFBWNDlmRDlCZUdrSUZZSVFBQUFBJCQAAAAAAAAAAAEAAACjR4oM0enWpNPKz-RhAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIYDWliGA1pYU; ispeed_lsm=0; __cfduid=ddeefc6dcbfbe4e8f2f42445af9b8efa31496637167; BAIDUID=9D393B3FB63846F80193F36834C6E1D3:FG=1; PSTM=1496906158; BD_HOME=1; BIDUPSID=CA2B4A9ED94F274DDC5D177C2236C7A0; BD_UPN=123353; BDRCVFR[FYP17ZXncD_]=mk3SLVN4HKm; BD_CK_SAM=1; PSINO=6; BDSVRTM=61; H_PS_PSSID=' -H 'Connection: keep-alive' --compressed
        ";
        if ($handle) {
            $i = 0 ;
            file_put_contents($baiduFile, "");
            while (($buffer = fgets($handle, 4096)) !== false) {
                $r = system(sprintf($cmd, trim($buffer)));
                $words = explode(',', $r);
                if($words){
                    file_put_contents($baiduFile, implode("\n", $words), FILE_APPEND);
                }
                echo sprintf("成功:%s\n", $i);;
                $i++;
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }else{
            echo "no file\n";
        }

    }


    public function actionInstallKeyWords(){
        $files = [
            "/home/kitral/shuguang/dull/console/controllers/dict/common_dict.dict",
            '/home/kitral/shuguang/dull/console/controllers/dict/hse_keyword.dict',
            "/home/kitral/shuguang/dull/console/controllers/dict/hse_product.dict",
            '/home/kitral/shuguang/dull/console/controllers/dict/hse_keyword_baidu.dict',
            '/home/kitral/shuguang/dull/console/controllers/dict/hse_user_realname.dict'
        ];
        // $r = $this->c()->indices()->delete(['index' => 'keyword02']);
        // $this->actionInstallKwIndex();
        $perNum = 1000;
        $indexes = [];
        foreach($files as $dictFile){
            list($filename, $ext) = explode('.', basename($dictFile));
            $handle = @fopen($dictFile, "r");
            if ($handle) {
                $i = 0;
                $params = ['body' => []];
                while (($buffer = fgets($handle, 4096)) !== false) {
                    $md5 = md5($buffer);
                    if(!array_key_exists($md5, $indexes)){
                        $indexes[$md5] = 1;
                        if($perNum === $i){
                            $br = $this->c()->bulk($params);
                            $params['body'] = [];
                            $i = 0;
                            echo "ok\n";
                        }
                        $params['body'][] = ['index' => ['_index' => 'keyword02', '_type' => 'keyword']];
                        $params['body'][] = ['value' => $buffer, 'type' => $filename];
                        $i++;
                    }
                }
                if(!empty($params['body'])){
                    $br = $this->c()->bulk($params);
                }
                if (!feof($handle)) {
                    echo "Error: unexpected fgets() fail\n";
                }
                fclose($handle);
            }else{
                echo "no file\n";
            }
        }
    }


    public function action1(){
        $str = file_get_contents('/home/kitral/shuguang/dull/console/controllers/data/post.txt');
        // // $url = 'http://localhost:1990/tokenizer/nlp';
        // $url = 'http://localhost:1990/extractor/keywords';
        // // $url = 'http://localhost:1990/extractor/keyphrase';
        // // $url = 'http://localhost:1990/extractor/sentence';
        // // $url = 'http://localhost:1990/extractor/summary';
        // $client = new \GuzzleHttp\Client();
        // $res = $client->request('POST', $url, [
        //     'form_params' => [
        //         // 'ret_pinyin' => false,
        //         // 'ret_pos' => false,
        //         'text' => $str
        //     ]
        // ]);
        // print_r( json_decode($res->getBody(), true));
        // $params = [
        //     'index' => 'demo01',
        //     // 'field' => 'value',
        //     'char_filters' => 'html_strip',
        //     'analyzer' => 'standard',
        //     'body' => [
        //         'text' => '<p>中国人</p>',
        //
        //     ]
        // ];
        // $c = $this->c()->indices()->analyze($params);
        // print_r($c);
        //

        // $data = [
        //     ['value' => 'hse安全项目'],
        //     ['value' => 'hse安全防爆项目'],
        //     ['value' => 'hse安全知识项目'],
        //     ['value' => '中文防爆项目' ],
        //     ['value' => '知识防爆项目']
        // ];
        // $params = ['body' => []];
        // foreach($data as $k => $item){
        //     $params['body'][] = ['index' => ['_index' => 'demo01', '_type' => 'keyword', '_id' => $k]];
        //     $params['body'][] = $item;
        // }
        // $this->c()->bulk($params);
        //
        $params = [
            'index' => 'demo01',
            'type' => 'keyword',
            'body' => [
                'query' => [
                    'match' => [
                        'value' => [
                            'query' => 'hse安全项目',
                            'type' => 'phrase',
                            'slop' => 20
                        ]
                    ]
                ]
            ]
        ];
        $r = $this->c()->search($params);
        console($r);


    }


    public function actionAnalyze(){
        $params = [
            'index' => 'demo01',
            'field' => 'value',
            // 'analyzer' => 'my',
            'body' => [
                'text' => "防爆的项目",
            ]
        ];
        $c = $this->c()->indices()->analyze($params);

        console($c);
    }
    public function action3(){
        $this->c()->indices()->delete(['index' => 'demo01']);
        $def = [
            'index' => 'demo01',
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0
                ],
                'mappings' => [
                    'keyword' => [
                        'properties' => [
                            'value' => [
                                'type' => 'string',
                                'analyzer' => 'ik_max_word'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $this->c()->indices()->create($def);
    }
    public function actionInstallKwIndex(){
        $this->c()->indices()->delete(['index' => 'keyword02']);
        $def = [
            'index' => 'keyword02',
            'body' => [
                'settings' => [
                    'index' => [
                        'search' => [
                            'slowlog' => [
                                'threshold' => [
                                    'fetch' => [
                                        'debug' => '10ms'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        'analyzer' => [
                            'cn_keywords_with_pinyin' => [
                                'type' => 'custom',
                                'tokenizer' => 'ik_max_word',
                                'filter' => ['cn_stop', "kw_length", "pinyin_filter" ,"unique"]
                            ],
                            'cn_keywords_smart_with_pinyin' => [
                                'type' => 'custom',
                                'tokenizer' => 'ik_smart',
                                'filter' => ['cn_stop', "kw_length", "pinyin_filter", "unique"]
                            ]
                        ],
                        "filter" => [
                            "pinyin_filter"  => [
                                "type" => "pinyin",
                                "keep_first_letter" => false,
                                "keep_full_pinyin" => true,
                                "keep_none_chinese" => true,
                                "keep_original" => true,
                                "keep_joined_full_pinyin" => true,
                                "limit_first_letter_length" => 16,
                                "lowercase" => true,
                                "trim_whitespace" => true,
                                "keep_none_chinese_in_first_letter" => true
                            ],
                            "cn_stop" => [
                                "type" =>       "stop",
                                'ignore_case' => true,
                                // todo
                                'stopwords_path' => '/tmp/dict/cn_stop_words.txt',
                            ],
                            "kw_length" => [
                                "type" => "length",
                                "min" => 1
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'keyword' => [
                        'properties' => [
                            'value' => [
                                'type' => 'string',
                                'analyzer' => 'cn_keywords_with_pinyin',
                                'fields' => [
                                    'smart_value' => [
                                        'type' => 'string',
                                        'analyzer' => 'cn_keywords_smart_with_pinyin'
                                    ]
                                ]
                            ],
                            'type' => [
                                'type' => 'string',
                                'index' => 'not_analyzed'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $a = $this->c()->indices()->create($def);
        print_r($a);
    }



    public function actionTest(){
        $r = $this->c()->indices()->get(['index' => 'multiple']);
        print_r($r);
    }
    public function actionQuery(){
        $params = [
            'index' => 'multiple',
            'body' => [
                'query' => [
                    'match_phrase' => [
                        'title' => [
                            'query' => '危险事故',
                            'slop' => 3
                        ],

                    ]
                    // 'multi_match' => [
                    //     'query' => '危险化学品事故',
                    //     'fields' => [
                    //         'title.long_word_title',
                    //         'title'
                    //     ],
                    //     'type' => 'cross_fields'
                    // ]
                ],
                'highlight' => [
                    "pre_tags" => ["<em>"],
                    "post_tags" => ["</em>"],
                    'fields' => [
                        'title' => new \StdClass(),
                        'industry' => new \StdClass()
                    ]
                ]
            ]
        ];
        $r = $this->c()->search($params);
        console($r);
    }
    public function actionLoad(){
        $data = json_decode(file_get_contents(Yii::getAlias('@console/controllers/data/pd.json')), true);
        $params = ['body' => []];
        foreach($data as $item){
            $params['body'][] = ['index' => ['_index' => 'multiple', '_type' => 'product', '_id' => $item['_id']]];
            unset($item['_id']);
            $params['body'][] = $item;
        }
        $r = $this->c()->bulk($params);
        print_r($r);
    }
    public function actionInstall(){
        $this->c()->indices()->delete(['index' => '_all']);
        $this->installIndexes();
        $this->actionLoad();
        $r = $this->c()->indices()->get(['index' => 'multiple']);
    }

    public function actionUpdate(){

    }
    private function importData(){

    }
    private function installIndexes(){
        $def = [
            'index' => 'multiple01',
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        'analyzer' => [
                            'cn_words' => [
                                'type' => 'custom',
                                'tokenizer' => 'ik_max_word',
                                'filter' => ['my_length', 'cn_stop', 'pinyin_filter', 'unique']
                            ],

                            'cn_long_words' => [
                                'type' => 'custom',
                                'tokenizer' => 'ik_smart',
                                'filter' => ['my_length', 'cn_stop', 'pinyin_filter', 'unique']
                            ]




                        ],
                        'filter' => [
                            "pinyin_filter"  => [
                                "type" => "pinyin",
                                "keep_first_letter" => false,
                                "keep_full_pinyin" => true,
                                "keep_none_chinese" => true,
                                "keep_original" => true,
                                "keep_joined_full_pinyin" => true,
                                "limit_first_letter_length" => 16,
                                "lowercase" => true,
                                "trim_whitespace" => true,
                                "keep_none_chinese_in_first_letter" => true
                            ],



                            "cn_stop" => [
                                "type" =>       "stop",
                                'ignore_case' => true,
                                // todo
                                'stopwords_path' => '/tmp/dict/cn_stop_words.txt',
                            ],
                            'my_length' => [
                                'type' => 'length',
                                'min' => 1
                            ]


                        ]
                    ]
                ],
                'mappings' => [
                    'product' => [
                        'properties' => [
                            'title' => [
                                'type' => 'string',
                                'analyzer' => 'cn_words',
                                'fields' => [
                                    'long_word_title' => [
                                        'type' => 'string',
                                        'analyzer' => 'cn_long_words'
                                    ]
                                ]
                            ],
                            'content' => [
                                'type' => 'string',
                                'analyzer' => 'cn_words',
                                'fields' => [
                                    'long_word_content' => [
                                        'type' => 'string',
                                        'analyzer' => 'cn_long_words'
                                    ]
                                ]
                            ],
                            'industry' => [
                                'type' => 'string',
                                'analyzer' => 'cn_words',
                                'fields' => [
                                    'long_word_industry' => [
                                        'type' => 'string',
                                        'analyzer' => 'simple'
                                    ]
                                ]
                            ],
                            'keyword' => [
                                'type' => 'string',
                                'analyzer' => 'cn_words',
                                'fields' => [
                                    'long_word_keyword' => [
                                        'type' => 'string',
                                        'analyzer' => 'simple'
                                    ]
                                ]
                            ],
                            'classification' => [
                                'type' => 'string',
                                'analyzer' => 'cn_words',
                                'fields' => [
                                    'long_word_classification' => [
                                        'type' => 'string',
                                        'analyzer' => 'simple'
                                    ]
                                ]
                            ],
                            'cls_id' => ['type' => 'integer'],
                            'format_id' => ['type' => 'integer'],
                            'approved' => ['type' => 'integer'],
                            'is_delete' => ['type' => 'integer'],
                            'uid' => ['type' => 'long'],
                            'create_time' => ['type' => 'long'],
                            'update_time' => ['type' => 'long'],
                        ]
                    ]
                ]
            ]
        ];
        $a = $this->c()->indices()->create($def);
        print_r($a);
    }
    private $_c;
    private function c(){
        if($this->_c){
            return $this->_c;
        }
        $hosts = [
                    "localhost:9200",
                ];
        return $this->_c = ClientBuilder::create()           // Instantiate a new ClientBuilder
                                    ->setHosts($hosts)      // Set the hosts
                                    ->build();;
    }
}
