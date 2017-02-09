<?php
namespace backend\controllers;

use common\base\AdminController;
use common\models\file\FileModel;
use common\models\file\File;

/**
 *
 */
class FileController extends AdminController
{
    public function actionSearch(){
        return $this->render('search');
    }

    public function actionSave(){
        $fileModel = new FileModel();
        $data = [
            'source_path' => '/home/kitral/Documents/abc.pdf',
            'source_path_type' => File::SP_LOCAL,
            'f_storage_type' => File::DR_OSS,
            'f_name' => 'english-action-tutorial',
            'f_prefix' => 'user/documents',
            'f_ext' => 'pdf',
            'f_acl_type' => File::FILE_ACL_PRI,
            'f_category' => 'no_category',
            'save_asyc' => false
        ];

        $result = $fileModel->saveFile($data);
        if(!$result){
            console($fileModel->getErrors());
        }else{
            console('ok');
        }
    }

}
