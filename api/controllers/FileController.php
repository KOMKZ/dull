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
use yii\imagine\Image;
use Imagine\Image\Box;



/**
 *
 */
class FileController extends ApiController
{
    private function ckError($error){
        $callbackNum = $_GET['CKEditorFuncNum'];
        $result = <<<JS
<script type="text/javascript">
    window.parent.CKEDITOR.tools.callFunction("{$callbackNum}", "", '{$error}');
</script>
JS;
        echo $result;
        exit();
    }

    private function ckSucc($url){
        $callbackNum = $_GET['CKEditorFuncNum'];
        $result = <<<JS
<script type="text/javascript">
    window.parent.CKEDITOR.tools.callFunction('{$callbackNum}', "{$url}", '');
</script>
JS;
        echo $result;
        exit();
    }

    public function checkFileTotal(){

    }


    public function actionAskChunkedFile(){
        $post = Yii::$app->request->post();
        switch ($post['ask_type']) {
            case 'checkFileTotal':

                break;
            case 'checkFileChunked':
                break;
            case 'checkFileMerge':
                break;
            default:
                // 注意任何的错误都会导致重新上传
                $this->error(null, '数据结构不正确');
                break;
        }
    }

    public function actionSaveChunkedFile(){
        $fileModel = new FileModel();
        $post = Yii::$app->request->post();

        return $this->succ(null, '1');
        // 准备上传目录
        $targetDir = Yii::$app->getAlias('@app/runtime/files/chunked_tmp');
        $uploadDir = Yii::$app->getAlias('@app/runtime/files/chunked_final');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir);
        }

        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;


    }

    public function actionSaveTmpCkImg(){
        try {
            $uploadFile = UploadedFile::getInstanceByName('upload');
            if(!$uploadFile){
                return $this->ckError(Yii::t('app', '服务器读取不到上传文件'));
            }

            $tempDir = Yii::getAlias('@api/runtime/files') . DIRECTORY_SEPARATOR;
            $fileName = uniqid(time(), true);
            $fileTotalName = $uploadFile->extension ? $fileName . '.' . $uploadFile->extension : $fileName;
            $filePath = $tempDir . $fileTotalName;
            if(!$uploadFile->saveAs($filePath)){
                Yii::error('保存临时文件出错' . $filePath);
                return $this->error(null, Yii::t('app', '保存文件出错'));
            }

            $fileModel = new FileModel();

            $data = [
                'source_path' => $filePath,
                'source_path_type' => File::SP_LOCAL,
                'f_storage_type' => File::DR_DISK,
                'f_category' => 'editor_image',
                'f_name' => $fileName,
                'f_acl_type' => File::FILE_ACL_PUB_R,
                // todo get setting
                'save_asyc' => false,
            ];
            $file = $fileModel->saveFile($data);
            if(!$file){
                list($code, $error) = $fileModel->getOneError();
                return $this->ckError($code.':'.$error);
            }else{
                unlink($filePath);
            }
            return $this->ckSucc($fileModel->getFileUrl($file));
        } catch (\Exception $e) {
            return $this->ckError($e->getMessage());
        }
    }


    public function actionSaveTmpCropImg(){
        $uploadFile = UploadedFile::getInstanceByName('file');
        if(!$uploadFile){
            return $this->error(null, Yii::t('app', '服务器读取不到上传文件'));
        }

        // 裁切文件
        $request = Yii::$app->request;
        $width = $request->post('width', 200);
        $height = $request->post('height', 200);
        $image = Image::crop(
            $uploadFile->tempName,
            intval($request->post('w')),
            intval($request->post('h')),
            [$request->post('x'), $request->post('y')]
        )->resize(
            new Box($width, $height)
        );

        // 保存裁切文件
        $tempDir = Yii::getAlias('@api/runtime/files') . DIRECTORY_SEPARATOR;
        $fileName = FileModel::generateUniqueName();
        $fileTotalName = $uploadFile->extension ? $fileName . '.' . $uploadFile->extension : $fileName;
        $filePath = $tempDir . $fileTotalName;
        if(!$image->save($filePath)){
            Yii::error('保存临时文件出错' . $filePath);
            return $this->error(null, Yii::t('app', '保存文件出错'));
        }

        // 保存入库
        $fileModel = new FileModel();

        $data = [
            'source_path' => $filePath,
            'source_path_type' => File::SP_LOCAL,
            'f_storage_type' => File::DR_DISK,
            'f_category' => 'image_crop',
            'f_name' => $fileName,
            'f_acl_type' => File::FILE_ACL_PUB_R,
            // todo get setting
            'save_asyc' => false,
        ];

        $file = $fileModel->saveFile($data);
        if(!$file){
            list($code, $error) = $fileModel->getOneError();
            return $this->error($code, $error);
        }else{
            unlink($filePath);
        }
        echo json_encode([
            'filelink' => $fileModel->getFileUrl($file),
            'file_name' => $file->getQueryId()
        ]);
        exit();
    }

    public function actionUpload(){
        console(1);
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
            return $this->error($uploadFile->error, Yii::t('app', '保存文件失败'));
        }

        // 实际存储 todo 数据结构需要改变
        $post = Yii::$app->request->post();
        if(empty($post['File'])){
            return $this->error(null, '数据结构错误');
        }

        // 构造入库的实际文件数据
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
