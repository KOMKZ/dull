<?php
namespace common\models\post;

use Yii;
use common\base\Model;
use common\models\post\tables\Post;
use yii\data\ActiveDataProvider;

/**
 *
 */
class PostModel extends Model
{
    public function getOne($condition){
        if(is_object($condition)){
            return $condition;
        }
        if($condition){
            return Post::find()->where($condition)->one();
        }else{
            return null;
        }
    }

    public function getProvider($condition = [], $sortData = [], $withPage = true){
        $query = Post::find();
        $query = $this->buildQueryWithCondition($query, $condition);

        $defaultOrder = [
            'p_created_at' => SORT_DESC
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
                'attributes' => ['p_created_at'],
                'defaultOrder' => $defaultOrder
            ]
        ]);
        $pagination = $provider->getPagination();
        return [$provider, $pagination];
    }

    public function createPost($data){
        $post = new Post();
        $post->scenario = 'create';
        $post->p_created_uid = Yii::$app->user->getid();

        if(!$post->load($data, '') || !$post->validate()){
            $this->addError('', $this->getArErrMsg($post));
            return false;
        }
        $result = $post->insert(false);
        if(!$result){
            $this->addError('', Yii::t('app', '写入失败'));
            return false;
        }
        return $post;
    }
}