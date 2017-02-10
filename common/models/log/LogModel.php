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
    public function getProvider($condition = [], $sortData = [], $withPage = true, $table){
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






    protected function getArAttributes(){
        return [
            'id', 'message', 'prefix', 'category', 'log_time', 'level'
        ];
    }


}
