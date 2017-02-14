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

    public function setBase($value){
        $this->_base = rtrim($value, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
    public function getBase(){
        return $this->_base;
    }
    public function outputByPath($filePath){
        $dir = md5(dirname($filePath)) . DIRECTORY_SEPARATOR . basename($filePath);
        $path = implode(DIRECTORY_SEPARATOR, [
            $this->_base,
            trim($dir, DIRECTORY_SEPARATOR.'.')
        ]);
        if(!file_exists($path)){
            throw new HttpException(404, Yii::t('app','文件不存在'));
        }
        $headers = Yii::$app->response->headers;
        return Yii::$app->response->sendFile($path);
    }
    public function output($file){
        $file->setSaveDir($this->_base);
        $path = $file->getFileSavePath();
        $headers = Yii::$app->response->headers;
        return Yii::$app->response->sendFile($path, $file->f_depostion_name);
    }
    public function getFileUrl($file){
        $frurl = Yii::$app->frurl;
        $frurl->hostInfo = $file->f_host;
        return $frurl->createAbsoluteUrl(['file/read', 'name' => $file->getFilePath()], 'http');
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
        copy($source, $target);
    }
    protected function prepareDir($file){
        $file->setSaveDir($this->base);
        $file->f_host = $this->_host;
        if(!is_dir($file->getSaveDir())){
            FileHelper::createDirectory($file->getSaveDir());
        }
    }
}
