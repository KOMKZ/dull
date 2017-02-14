<?php
namespace api\controllers;

use Yii;
use yii\helpers\Json;
use common\base\ApiController;
use common\models\file\FileModel;
use common\models\file\File;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\web\Response;

/**
 *
 */
class FileController extends ApiController
{


    public function actionUpload(){
        // 首先先上传文件
        $file = new File();
        $uploadFile = UploadedFile::getInstance($file, 'upload_file');
        if(!$uploadFile){
            return $this->error(null, Yii::t('app', '服务器读取不到上传文件'));
        }
        $file->upload_file = $uploadFile;

        $tempDir = Yii::getAlias('@api/runtime/files') . DIRECTORY_SEPARATOR;
        $fileName = uniqid(time(), true);
        $fileTotalName = $uploadFile->extension ? $fileName . '.' . $uploadFile->extension : $fileName;
        $filePath = $tempDir . $fileTotalName;
        if (!$uploadFile->saveAs($filePath)) {
            return $this->error($uploadFile->error);
        }
        // 实际存储 todo 数据结构需要改变
        $post = Yii::$app->request->post();
        if(empty($post['File'])){
            return $this->error(null, '数据结构错误');
        }
        $post['File']['source_path'] = $filePath;
        $post['File']['source_path_type'] = File::SP_LOCAL;
        $file->upload_file = $uploadFile;
        if(empty($post['File']['f_name'])){
            $post['File']['f_name'] = $fileName;
        }
        $fileModel = new FileModel();
        $result = $fileModel->saveFile($post['File'], $file);
        if(!$result){
            list($code, $error) = $fileModel->getOneError();
            return $this->error($code, $error);
        }
        $files = [
            'files' => [
                [
                    'name' => $file->f_name,
                    'size' => $file->f_size,
                    'url' => $fileModel->getFileUrl($file),
                    'thumbnailUrl' => '',
                    'deleteUrl' => '',
                    'deleteType' => 'POST'
                ]
            ]
        ];
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->data = $files;

    }

}
