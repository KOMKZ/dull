<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController;
use common\models\file\FileModel;
use common\models\file\File;
use yii\web\UploadedFile;
use common\helpers\ExifTool;

/**
 *
 */
class FileController extends AdminController
{
    public function actionList(){
        $fileModel = new FileModel();
        list($provider, $pagination) = $fileModel->getProvider();
        return $this->render('list', [
            'provider' => $provider,
            'map' => File::getValidConsts()
        ]);
    }


    public function actionFileView($id){
        $fileModel = new FileModel();
        $file = $fileModel->getOne(['f_id' => $id]);
        if(!$file){
            return $this->notfound();
        }

        return $this->render('view', [
            'model' => $file,
            'map' => File::getValidConsts(),
            'fileUrl' => $fileModel->getFileUrl($file->f_id),
            'fileMeta' => $file->getMetaObj(),
        ]);
    }

    public function actionAdd(){
        $fileModel = new FileModel();
        $file = new File();
        return $this->render('add', [
            'model' => $file,
            'map' => File::getValidConsts(),
            'fileUploadUrl' => Yii::$app->apiurl->createAbsoluteUrl(['file/upload']),
        ]);
    }

    public function actionSave(){
        $fileModel = new FileModel();
        $data = [
            'source_path' => '/home/kitral/Documents/requests.pdf',
            'source_path_type' => File::SP_LOCAL,
            'f_storage_type' => File::DR_OSS,
            'f_name' => 'requests',
            'f_prefix' => 'user/documents',
            'f_ext' => 'pdf',
            'f_acl_type' => File::FILE_ACL_PRI,
            'f_category' => 'no_category',
            'save_asyc' => true
        ];

        $result = $fileModel->saveFile($data);
        if(!$result){
            console($fileModel->getErrors());
        }else{
            console('ok');
        }
    }

}
