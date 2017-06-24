<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class EsController extends Controller{



    public function action1($text = ' ', $type = 1){
        $a = $type == 1 ? 'a_search_words' : "a_search_pinyin_words";
        $es = Yii::$app->es;
        $params = [
            'index' => 'hse2data',
            'analyzer' => $a,
            'text' => $text
        ];
        $r = $es->indices()->analyze($params);
        console($r['tokens']);
        console(ArrayHelper::getColumn($r['tokens'], 'token'));
    }
    public function action2(){
        $es = Yii::$app->es;
        $def = [
            'index' => 'hse2data',
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        'analyzer' => [
                            'a_search_words' => [
                                'type' => 'custom',
                                'tokenizer' => 'ik_max_word',
                                'filter' => ['f_trim_empty']
                            ],
                            'a_search_pinyin_words' => [
                                'type' => 'custom',
                                'tokenizer' => 'ik_max_word',
                                'filter' => ['f_keep_pinyin', 'f_trim_empty']
                            ],
                            'a_ik_words' => [
                                'type' => 'custom',
                                'tokenizer' => 'ik_max_word',
                                'filter' => ['f_trim_empty']
                            ],
                            'a_ik_pinyin_words' => [
                                'type' => 'custom',
                                'tokenizer' => 't_ik_pinyin',
                                'filter' => ['f_pinyin_max_length', 'f_trim_empty']
                            ],

                        ],
                        'tokenizer' => [
                            't_ik_pinyin' => [
                                'type' => 'pinyin',
                                "remove_duplicated_term" => true,
                                "keep_first_letter" => false, // 刘德华>ldh
                                // "keep_separate_first_letter" => false, //刘德华>l,d,h
                                "keep_full_pinyin" => true,
                                "keep_none_chinese" => true,
                                "keep_none_chinese_together" => true,
                                // "none_chinese_pinyin_tokenize" => true,
                                // "keep_original" => true,
                                "keep_joined_full_pinyin" => true,
                                // "limit_first_letter_length" => 16,
                                "lowercase" => true,
                                "trim_whitespace" => true,
                                // "keep_none_chinese_in_first_letter" => false
                            ]
                        ],
                        "filter" => [
                            'f_pinyin_max_length' => [
                                'type' => 'length',
                                'max' => 30
                            ],
                            'f_trim_empty' => [
                                'type' => 'length',
                                'min' => 1
                            ],
                            "f_keep_pinyin"  => [
                                "type" => "pinyin",
                                "remove_duplicated_term" => true,
                                "keep_first_letter" => false, // 刘德华>ldh
                                "keep_separate_first_letter" => false, //刘德华>l,d,h
                                "keep_full_pinyin" => true,
                                "keep_none_chinese" => true,
                                "keep_none_chinese_together" => true,
                                "none_chinese_pinyin_tokenize" => true,
                                "keep_original" => false,
                                "keep_joined_full_pinyin" => true,
                                "limit_first_letter_length" => 16,
                                "lowercase" => true,
                                "trim_whitespace" => true,
                                "keep_none_chinese_in_first_letter" => false
                            ],
                            "f_pinyin_filter"  => [
                                "type" => "pinyin",
                                "remove_duplicated_term" => true,
                                "keep_first_letter" => false, // 刘德华>ldh
                                "keep_separate_first_letter" => false, //刘德华>l,d,h
                                "keep_full_pinyin" => true,
                                "keep_none_chinese" => true,
                                "keep_none_chinese_together" => true,
                                "none_chinese_pinyin_tokenize" => true,
                                "keep_original" => true,
                                "keep_joined_full_pinyin" => true,
                                "limit_first_letter_length" => 16,
                                "lowercase" => true,
                                "trim_whitespace" => true,
                                "keep_none_chinese_in_first_letter" => false
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'discuss' => [
                        'properties' => [
                            'title' => [
                                'type' => 'string',
                                'analyzer' => 'a_ik_words',
                                'term_vector' => 'with_positions_offsets',
                                'fields' => [
                                    'pinyin' => [
                                        'term_vector' => 'with_positions_offsets',
                                        'type' => 'string',
                                        'analyzer' => 'a_ik_pinyin_words'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $exists = $es->indices()->exists(['index' => 'hse2data']);
        if($exists){
            $es->indices()->delete(['index' => 'hse2data']);
        }
        $r = $es->indices()->create($def);
        console($r);
    }

    public function action3(){
        $a = Yii::$app->es->index([
            'index' => 'hse2data',
            'type' => 'discuss',
            'id' => 1,
            'body' =>[
                'title' => '中国人是很强大的哦！不服来辩论。'
            ]
        ]);
        Yii::$app->es->index([
            'index' => 'hse2data',
            'type' => 'discuss',
            'id' => 2,
            'body' =>[
                'title' => '2015年中国超越了美国成为世界第一。'
            ]
        ]);
    }

    public function action4($text = ' '){
        $params = [
            'index' => 'hse2data',
            'type' => 'discuss',
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            [
                                'match' => [
                                    'title' => [
                                        'query' => $text,
                                        // 'minimum_should_match' => '100%'
                                        'analyzer' => 'a_search_words'
                                    ]
                                ]
                            ],
                            [
                                'match' => [
                                    'title.pinyin' => [
                                        'query' => $text,
                                        // 'minimum_should_match' => '100%'
                                        'analyzer' => 'a_search_pinyin_words'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'highlight' => [
                    'fields' => [
                        'title' => [
                            "matched_fields" => ["title", "title.pinyin"],
                            'type' => 'fvh'
                        ]
                        // 'title' => new \StdClass(),
                        // 'title.pinyin' => new \StdClass()
                        // 'title' => [
                        //     'type' => 'postings-highlighter'
                        // ]
                        // 'title' => [
                            // "number_of_fragments"=> 1,
                            // "type"=> "experimental"
                            // "number_of_fragments"=> 3,
                            // "type"=> "experimental",
                            //  'options' => [
                            //      "skip_if_last_matched" => true
                            //  ]
                        // ]
                    ]
                ]
            ]
        ];

        $r = Yii::$app->es->search($params);
        if(!empty($r['hits']['hits'])){
            console($r['hits']['hits']);
        }

    }



    /**
     * 安装银行词汇索引和初始化索引
     * @return [type] [description]
     */
    public function actionInstallBank(){
        try {
            $bIndexDef = $this->getIndexDef('bank');
            $es = Yii::$app->es;
            $bIndexExists = $es->indices()->exists(['index' => 'bank']);
            if($bIndexExists){
                $es->indices()->delete(['index' => 'bank']);
            }
            $r = $es->indices()->create($bIndexDef);
            // 索引
            $bData = require_once(Yii::getAlias('@common/config/es-data/es-bank.php'));
            $params = ['body' => []];
            foreach($bData as $b){
                $params['body'][] = ['index' => ['_index' => 'bank', '_type' => 'bank']];
                $params['body'][] = ['value' => $b['value'], 'type' => $b['code']];
            }
            $r = $es->bulk($params);
            if(!$r['errors']){
                echo sprintf("succ:%sms, %s affects.\n", $r['took'], count($r['items']));
                return 0;
            }else{
                Yii::error($r['errors']);
                echo "error:please check log. \n";
                return 0;
            }
        } catch (\Exception $e) {
            Yii::error($e);
            echo "error: please check log. " . $e->getMessage() . "\n";
            return 0;
        }
    }
    /**
     * 测试查询银行接口
     * @param  string $text [description]
     * @return [type]       [description]
     */
    public function actionQueryBank($text = ''){
        $es = Yii::$app->es;
        $bIndexExists = $es->indices()->exists(['index' => 'bank']);
        if(!$bIndexExists){
            echo "error:index bank done\'s exists.\n";
            return 1;
        }
        if(empty(trim($text))){
            $query = [
                'match_all' => new \StdClass()
            ];
            $size = 200;
        }else{
            $query = [
                'match' => [
                    'value' => [
                        'query' => $text,
                        'analyzer' => preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $text) ? 'ik_max_word' : 'cn_keywords_with_pinyin',
                    ]
                ]
            ];
            $size = 20;
        }
        $params = [
            'index' => 'bank',
            'type' => 'bank',
            'size' => $size,
            'explain' => true,
            'body' => [
                'query' => $query,
                'sort' => [
                    ['_score' => ['order' => 'desc']],
                    ['value' => ['order' => 'asc'] ]
                ]
            ]
        ];
        $r = $es->search($params);
        if(empty($r['hits']['hits'])){
            echo sprintf("count:%s\n", 0);
            return 0;
        }else{
            echo sprintf("count:%s\n", $r['hits']['total']);
            foreach($r['hits']['hits'] as $item){
                echo sprintf("%s %s\n", $item['_source']['value'], $item['_score']) ;
            }
        }
    }



    protected function getIndexDef($name){
        $defs = require_once(Yii::getAlias('@common/config/es/indices-def.php'));
        if(array_key_exists($name, $defs)){
            return $defs[$name];
        }
        throw new \Exception("index {$name} does't exists.");
    }

}
