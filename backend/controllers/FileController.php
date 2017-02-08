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
        $file = $fileModel->newFile();
        $result = $fileModel->saveFile($file);
        if(!$result){
            console($result);
        }else{
            console('ok');
        }
    }

}
