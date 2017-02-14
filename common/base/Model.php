<?php
namespace common\base;

use yii\base\Model as BaseModel;

/**
 *
 */
class Model extends BaseModel
{
    public function getOneError(){
        $errors = $this->getFirstErrors();
        if(!empty($errors)){
            foreach($errors as $code => $msg){
                return [$code, $msg];
            }
        }else{
            return [null, null];
        }
    }

    public function getArErrMsg($obj){
        return ':' . implode(',', $obj->getFirstErrors());
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
}
