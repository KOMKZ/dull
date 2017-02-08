<?php
namespace backend\controllers;

use common\base\AdminController;
use common\models\file\FileModel;

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
            'sourcePath' => '/home/ktiral/Documents/abc.pdf',
            'path_type' => File::PATH_TYPE_LOCAL,
        ];
        $file = $fileModel->newFile();
        if(!$file->load($data, '') || $file->hasErrors()){
            console($file->getErrors());
        }

        $result = $fileModel->saveFile($file);
        if(!$result){
            console($result);
        }else{
            console('ok');
        }
    }

}
