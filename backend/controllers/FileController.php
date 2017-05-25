<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController;
use common\models\file\FileModel;
use common\models\file\File;
use yii\web\UploadedFile;
use common\helpers\ExifTool;
use yii\helpers\Url;

/**
 *
 */
class FileController extends AdminController
{

    public function actionWufileupload(){

        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");


        // Support CORS
        // header("Access-Control-Allow-Origin: *");
        // other CORS headers if any...
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit; // finish preflight CORS requests here
        }


        if ( !empty($_REQUEST[ 'debug' ]) ) {
            $random = rand(0, intval($_REQUEST[ 'debug' ]) );
            if ( $random === 0 ) {
                header("HTTP/1.0 500 Internal Server Error");
                exit;
            }
        }

        // header("HTTP/1.0 500 Internal Server Error");
        // exit;


        // 5 minutes execution time
        @set_time_limit(5 * 60);

        // Uncomment this one to fake upload time
        // usleep(5000);

        // Settings
        // $targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $targetDir = '/tmp/wu1';
        $uploadDir = '/tmp/wu2';

        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds


        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }

        // Create target dir
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir);
        }

        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }

        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        $uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        // Chunking might be enabled
        // 分片的序号
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        // 分片的数量
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;


        // Remove old temp files
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }

            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
        console($file);

                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }

                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }


        // Open temp file
        if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }

        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
            }

            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }

        } else {
            if (!$in = @fopen("php://input", "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);

        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");

        $index = 0;
        $done = true;
        for( $index = 0; $index < $chunks; $index++ ) {
            if ( !file_exists("{$filePath}_{$index}.part") ) {
                $done = false;
                break;
            }
        }
        if ( $done ) {
            if (!$out = @fopen($uploadPath, "wb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            }

            if ( flock($out, LOCK_EX) ) {
                for( $index = 0; $index < $chunks; $index++ ) {
                    if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
                        break;
                    }

                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }

                    @fclose($in);
                    @unlink("{$filePath}_{$index}.part");
                }

                flock($out, LOCK_UN);
            }
            @fclose($out);
        }

        // Return Success JSON-RPC response
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }

    public function actionDemo(){
        $fileModel = new FileModel();
        $url = [
            'file/save-chunked-file' => Yii::$app->apiurl->createAbsoluteUrl(['file/save-chunked-file']),
            'file/ask-chunked-file' => Yii::$app->apiurl->createAbsoluteUrl(['file/ask-chunked-file'])
        ];
        return $this->render('demo', [
            'url' => $url
        ]);
    }

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
    public function actionDelete($id){
        $fileModel = new FileModel();
        console($fileModel->deleteOneFile(['f_id' => $id]));
    }
    public function actionSave(){
        $fileModel = new FileModel();
        $data = [
            'source_path' => '/home/kitral/Documents/requests.pdf',
            'source_path_type' => File::SP_LOCAL,
            'f_storage_type' => File::DR_OSS,
            // 'f_name' => 'requests',
            'f_prefix' => '',
            'f_ext' => 'pdf',
            'f_acl_type' => File::FILE_ACL_PRI,
            'f_category' => 'no_category',
            'save_asyc' => false
        ];

        $file = $fileModel->saveFile($data);
        if(!$file){
            console($fileModel->getErrors());
        }else{
            console($file->toArray());
        }
    }

}
