<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;

class EsController extends Controller{
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

    public function action1(){
        $r = Yii::$app->es->indices()->analyze([
            'index' => 'bank',
            // 'field' => 'value',
            'analyzer' => 'ik_max_word',
            'text' => '招商'
        ]);
        console($r);
    }

    protected function getIndexDef($name){
        $defs = require_once(Yii::getAlias('@common/config/es/indices-def.php'));
        if(array_key_exists($name, $defs)){
            return $defs[$name];
        }
        throw new \Exception("index {$name} does't exists.");
    }

}
