<?php
namespace frontend\controllers;

use Yii;
use common\base\FrController;
use common\models\file\FileModel;
use common\models\file\File;
use yii\web\HttpException;

/**
 *
 */
class FileController extends FrController
{
    public function actionIndex($id){
        $fileModel = new FileModel();
        $url = $fileModel->getFileUrl($id);
        console($url);
    }
    public function actionRead($name){
        list($saveType, $path) = FileModel::parseQueryId($name);
        if($saveType){
            $driver = FileModel::instanceDriver($saveType);
            $driver->outputByQid($name);
        }else{
            Header("HTTP/1.1 404 Not Found");
        }
    }


}
