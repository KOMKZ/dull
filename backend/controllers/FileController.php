<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController;
use common\models\file\FileModel;
use common\models\file\File;
use yii\web\UploadedFile;

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
            'fileStatusMap' => File::getValidConsts('f_status'),
            'fileStorageTypeMap' => File::getValidConsts('f_storage_type')
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
            'fileStatusMap' => File::getValidConsts('f_status'),
            'fileCategoryMap' => File::getValidConsts('f_category'),
            'fileStorageTypeMap' => File::getValidConsts('f_storage_type'),
            'fileAclTypeMap' => File::getValidConsts('f_acl_type'),
            'fileUrl' => $fileModel->getFileUrl($file->f_id)
        ]);
    }

    public function actionAdd(){
        $fileModel = new FileModel();
        $file = new File();
        return $this->render('add', [
            'model' => $file,
            'filePathTypeMap' => File::getValidConsts('source_path_type'),
            'fileStorageTypeMap' => File::getValidConsts('f_storage_type'),
            'fileAclTypeMap' => File::getValidConsts('f_acl_type'),
            'fileCategoryMap' => File::getValidConsts('f_category'),
            "fileSaveAsycMap" => File::getValidConsts('save_asyc'),
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
