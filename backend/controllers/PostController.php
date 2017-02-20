<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController;
use common\models\post\tables\Post;
use common\models\post\PostModel;
use common\models\file\FileModel;

class PostController extends AdminController
{

    public function actionList(){
        $postModel = new PostModel();
        list($provider, $pagination) = $postModel->getProvider();
        return $this->render('list', [
            'provider' => $provider
        ]);
    }

    public function actionAdd(){
        $post = new Post();
        if(Yii::$app->request->isPost){
            $postData = Yii::$app->request->post();
            // $fileModel = new FileModel();
            // $file = $fileModel->uploadTmpFile($postData['Post']['p_thumb_img']);
            // if(!$file){
            //     list($code, $error) = $fileModel->getOneError();
            //     $this->error($code, $error);
            //     return $this->refresh();
            // }
            $postModel = new PostModel();
            $data = $postData['Post'];


            $result = $postModel->createPost($data);
            if(!$result){
                list($code, $error) = $postModel->getOneError();
                $this->error($code, $error);
                return $this->refresh();
            }else{
                $this->succ();
                return $this->refresh();
            }

        }
        return $this->render('add', [
            'model' => $post,
            'postContentTypeMap' => Post::getValidConsts('p_content_type'),
            'postStatusMap' => Post::getValidConsts('p_status'),
            'fileUploadUrl' => Yii::$app->apiurl->createAbsoluteUrl(['file/save-tmp-crop-img'], 'http'),
        ]);
    }

    public function actionUpdate(){
        return $this->render('update');
    }

    public function actionView($id){
        $postModel = new PostModel();
        $post = $postModel->getOne(['p_id' => $id]);
        if(!$post){
            return $this->notfound();
        }
        return $this->render('view', [
            'model' => $post,
            'postContentTypeMap' => Post::getValidConsts('p_content_type'),
            'postStatusMap' => Post::getValidConsts('p_status')
        ]);
    }



}
