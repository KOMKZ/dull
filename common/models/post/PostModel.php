<?php
namespace common\models\post;

use Yii;
use common\base\Model;
use common\models\post\tables\Post;
use yii\data\ActiveDataProvider;
use common\models\file\FileModel;

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
    public function updatePost($condition, $data){
        // Yii::$app->db->beginTransaction();
        $post = $this->getOne($condition);
        if(!$post){
            $this->addError('', Yii::t('app', '文章不存在'));
            return false;
        }
        $post->scenario = 'update';

        if(!$post->load($data, '') || !$post->validate()){
            $this->addError('', $this->getArErrMsg($post));
            return false;
        }
        $fileModel = new FileModel();
        $result = $fileModel->setFilePermanentFromContent($data['p_content'], $post->p_content);
        if(!$result){
            $this->addError('', Yii::t('app', '设置文件时效出错'));
            return false;
        }
        if(urldecode($post['p_thumb_img']) != urldecode($data['p_thumb_img'])){
            $file = $fileModel->getOneByQueryId($data['p_thumb_img']);
            if(!$file){
                $this->addError('', Yii::t('app', '保存的文件不存在'));
                return false;
            }
            $result = $fileModel->setFilePermanentFromArray([$data['p_thumb_img']], [$post->p_thumb_img_id]);
            if(!$result){
                $this->addError('', Yii::t('app', '设置文件时效出错'));
                return false;
            }
            $post->p_thumb_img = $fileModel->getFileUrl($file);
            $post->p_thumb_img_id = $data['p_thumb_img'];
            // 标记原来的图片已经废弃
        }

        $result = $post->update(false);
        if(false === $result){
            $this->addError('', Yii::t('app', '写入失败'));
            return false;
        }
        return $post;

    }
    public function createPost($data){
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $post = new Post();
            $post->scenario = 'create';
            $post->p_created_uid = Yii::$app->user->getid();

            if(!$post->load($data, '') || !$post->validate()){
                $this->addError('', $this->getArErrMsg($post));
                return false;
            }
            $fileModel = new FileModel();
            // 设置文章内容的文件为有效
            if(!empty($data['p_content'])){
                $result = $fileModel->setFilePermanentFromContent($data['p_content']);
                if(!$result){
                    $this->addError('', Yii::t('app', '设置文件时效出错'));
                    return false;
                }
            }

            // 保存图片
            if(!empty($data['p_thumb_img'])){
                // todo 应该要有一个保护机制，保护这个文件只属于这次内容
                $file = $fileModel->getOneByQueryId($data['p_thumb_img']);
                if(!$file){
                    $this->addError('', Yii::t('app', '保存的文件不存在'));
                    return false;
                }
                $result = $fileModel->setFilePermanentFromArray([$data['p_thumb_img']]);
                if(!$result){
                    $this->addError('', Yii::t('app', '设置文件时效出错'));
                    return false;
                }
                $post->p_thumb_img = $fileModel->getFileUrl($file);
                $post->p_thumb_img_id = $data['p_thumb_img'];
            }

            $result = $post->insert(false);
            if(!$result){
                $this->addError('', Yii::t('app', '写入失败'));
                return false;
            }
            $transaction->commit();
            return $post;
        } catch (\Exception $e) {
            Yii::error($e);
            $transaction->rollback();
            $this->addError('', '发生异常');
            return false;
        }
    }
}
