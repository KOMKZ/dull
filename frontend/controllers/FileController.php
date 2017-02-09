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
        Yii::$app->diskfile->outputByPath($name);
        // $file = $fileModel->getOne(['f_id' => $id]);
        // if(!$file){
        //     throw new HttpException(404, Yii::t('文件不存在'));
        // }
        // $fileModel->output($file);
    }


}
