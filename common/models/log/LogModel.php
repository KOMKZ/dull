<?php
namespace common\models\log;

use Yii;
use common\base\Model;
use yii\log\DbTarget;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\data\ArrayDataProvider;


/**
 *
 */
class LogModel extends Model
{

    public function getOne($condition, $table){
        $one = (new Query())
                ->from($table)
                ->where($condition)
                ->one();
        return $one;
    }
    public function isLogTableExists($tableName){
        return Yii::$app->db->getTableSchema($tableName, true) ? true : false;
    }
    public function getProvider($condition = [], $sortData = [], $table, $withPage = true){
        $query = (new Query())
                ->from($table);
        $query = $this->buildQueryWithCondition($query, $condition);

        $defaultOrder = [
            'log_time' => SORT_DESC
        ];

        if(!empty($sortData)){
            $defaultOrder = $sortData;
        }
        $pageConfig = [];
        if(!$withPage){
            $pageConfig['pageSize'] = 0;
        }else{
            $pageConfig['pageSize'] = 10;
        }
        $provider = new ArrayDataProvider([
            'allModels' => $query->all(),
            'pagination' => $pageConfig,
            'sort' => [
                'attributes' => ['log_time'],
                'defaultOrder' => $defaultOrder
            ]
        ]);
        $pagination = $provider->getPagination();
        return [$provider, $pagination];
    }


    protected function buildQueryWithCondition($query, $condition){
        $arrayCondition = [];
        foreach($condition as $key => $value){
            if(is_array($value)){
                $arrayCondition[$key] = $value;
                unset($condition[$key]);
            }elseif (preg_match('/[\w_\-]+,[\w_\-]+/', $value)) {
                $candicates = preg_split('/\s*,\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
                $arrayCondition[$key] = ['in' => $candicates];
                unset($condition[$key]);
            }
        }
        $query = $query->where($condition);
        foreach($arrayCondition as $name => $item){
            if(array_key_exists('start', $item) && !empty($item['start'])){
                $query->andWhere(['>=', $name, $item['start']]);
            }
            if(array_key_exists('end', $item) && !empty($item['end'])){
                $query->andWhere(['<=', $name, $item['end']]);
            }
            if(array_key_exists('in', $item) && !empty($item['in'])){
                $query->andWhere(['in', $name, $item['in']]);
            }
            if(array_key_exists('like', $item) && !empty($item['like'])){
                $query->andWhere(['like', $name, $item['like']]);
            }
        }
        return $query;
    }

    public function parseQueryCondtion($data){
        $attributes = $this->getArAttributes();
        foreach($data as $name => $item){
            if(!in_array($name, $attributes) || empty($item)){
                unset($data[$name]);
            }
        }
        return $data;
    }

    public function parseQuerySort($data){
        $attributes = $this->getArAttributes();
        if(array_key_exists('sort', $data) && is_array($data['sort'])){
            $sortData = $data['sort'];
            foreach($sortData as $name => $item){
                if(!in_array($name, $attributes) || empty($item) || !in_array($item, [1, -1])){
                    unset($sortData[$name]);
                }elseif(1 == $item){
                    $sortData[$name] = SORT_DESC;
                }elseif(-1 == $item){
                    $sortData[$name] = SORT_ASC;
                }
            }
        }else{
            $sortData = [];
        }
        return $sortData;
    }

    protected function getArAttributes(){
        return [
            'id', 'message', 'prefix', 'category', 'log_time', 'level'
        ];
    }


}
