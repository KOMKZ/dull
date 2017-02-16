<?php
namespace common\models\user;

use common\base\Model;
use common\models\user\tables\UserGroup;
use yii\data\ActiveDataProvider;

/**
 *
 */
class GroupModel extends Model
{
    public function getOne($condition){
        if(is_object($condition)){
            return $condition;
        }
        if($condition){
            return UserGroup::find()->where($condition)->one();
        }else{
            return null;
        }
    }

    public function getProvider($condition = [], $sortData = [], $withPage = true){
        $query = UserGroup::find();
        $query = $this->buildQueryWithCondition($query, $condition);

        $defaultOrder = [
            'ug_created_at' => SORT_DESC
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
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pageConfig,
            'sort' => [
                'attributes' => ['ug_created_at'],
                'defaultOrder' => $defaultOrder
            ]
        ]);
        $pagination = $provider->getPagination();
        return [$provider, $pagination];
    }
}
