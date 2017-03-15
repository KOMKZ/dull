<?php
namespace common\models\file;

use Yii;
use common\base\Model;
use common\models\file\File;
use yii\helpers\FileHelper;
use yii\web\HttpException;

/**
 *
 */
class DiskDriver extends Model
{
    private $_base = null;
    private $_host = null;
    public function init(){
        $this->initCheck();
    }
    protected function initCheck(){
        if(!is_dir($this->base)){
            throw new \Exception(sprintf('%s base目录不存在, %s', self::className(), $this->base));
        }
        if(!is_writable($this->base)){
            throw new \Exception(sprintf('%s base目录不可写, %s', self::className(), $this->base));
        }
    }
    public function setHost($value){
        $this->_host = rtrim($value, DIRECTORY_SEPARATOR);
    }
    public function getHost(){
        return $this->_host;
    }

    public function sedeleteFiletBase($value){
        $this->_base = rtrim($value, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
    public function getBase(){
        return $this->_base;
    }
    public function setBase($value){
        return $this->_base = $value;
    }
    public function outputByPath($filePath){
        $path = $this->getFilePath($filePath);
        if(!file_exists($path)){
            throw new HttpException(404, Yii::t('app','文件不存在'));
        }
        $headers = Yii::$app->response->headers;
        return Yii::$app->response->sendFile($path);
    }
    public function deleteFile($fileName){
        $filePath = $this->getFilePath($fileName);
        if(!file_exists($filePath)){
            return true;
        }
        // todo 这里要严格校验路径是否在base之内
        @unlink($filePath);
        return true;
    }
    public function output($file){
        $path = $file->getFileSavePath();
        $headers = Yii::$app->response->headers;
        return Yii::$app->response->sendFile($path, $file->f_depostion_name);
    }
    public function getFileUrl($filePath, $host = ''){
        $frurl = Yii::$app->frurl;
        if(!empty($host)){
            $frurl->hostInfo = $host;
        }
        return $frurl->createAbsoluteUrl(['file/read', 'name' => $filePath], 'http');
    }
    public function getFilePath($name){
        // $dir = md5(dirname($name)) . DIRECTORY_SEPARATOR . basename($name);
        $path = implode(DIRECTORY_SEPARATOR, [
            rtrim($this->_base, DIRECTORY_SEPARATOR),
            trim($name, DIRECTORY_SEPARATOR.'.')
        ]);
        return $path;
    }
    public function save(File $file){
        if($file->hasErrors()){
            $this->addErrors($file->getErrors());
            return false;
        }
        $this->prepareDir($file);
        $this->copyFile($file->source_path, $file->getFileSavePath());
        return $file;
    }
    protected function copyFile($source, $target){
        // todo 解决权限的问题
        copy($source, $target);
    }
    protected function prepareDir($file){
        $file->setSaveDir($this->_base);
        $file->f_host = $this->_host;
        if(!is_dir($file->getSaveDir())){
            FileHelper::createDirectory($file->getSaveDir());
            chmod($file->getSaveDir(), 0777);
        }
    }
}
