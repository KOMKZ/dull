<?php
namespace common\models\file;

use Yii;
use common\base\Model;
use OSS\OssClient as Oss;

/**
 *
 */
class OssDriver extends Model
{
    static private $oss;
    static private $innerOss;
    private $_base = null;

    public $bucket = null;
    public $access_key_id = null;
    public $access_secret_key = null;
    private $_is_cname = null;
    public $endpoint = null;
    public $inner_endpoint = null;

    public function getFileUrl($filePath, $host = ''){
        if($file->isPublic){
            $host = $this->getHostName();
            $objectId = $this->buildFilePath($filePath);
            return "http://" . implode('/', [$host, $objectId]);
        }elseif($file->isPrivate){

            $objectId = $this->buildFilePath($filePath);
            return $this->instanceOss()->signUrl($this->bucket, $objectId, 3600);
        }
    }

    public function save(File $file){
        if($file->hasErrors()){
            $this->addErrors($file->getErrors());
            return false;
        }
        $objectName = $this->buildFilePath($file->getFilePath());
        try {
            $this->upload($this->bucket, $objectName, $file->source_path, []);
            if($file->isPublic){
                $this->setFilePublic($this->bucket, $objectName);
            }
            $file->f_host = $this->bucket;
            return $file;
        } catch (\Exception $e) {
            $this->addError('', $e->getMessage());
            return false;
        }
    }

    public function upload($bucket, $objName, $sourcePath, $options = []){
        $options[Oss::OSS_CONTENT_LENGTH] = filesize($sourcePath);
        return $this->instanceOss(true)->uploadFile($bucket, $objName, $sourcePath, $options);
    }

    public function setFilePublic($bucket, $objName){
        $this->instanceOss(true)->putObjectAcl($bucket, $objName, Oss::OSS_ACL_TYPE_PUBLIC_READ_WRITE);
    }



    protected function instanceOss($inner = false){
        if(!$inner){
            if(null === self::$oss){
                self::$oss = new Oss($this->access_key_id, $this->access_secret_key, $this->endpoint, $this->is_cname);
            }
            return self::$oss;
        }else{
            if(null === self::$innerOss){
                self::$innerOss = new Oss($this->access_key_id, $this->access_secret_key, $this->inner_endpoint, $this->is_cname);
            }
            return self::$innerOss;
        }
    }

    protected function getHostName($inner=false){
        if($inner){
            return implode('.', [$this->bucket, $this->inner_endpoint]);
        }else{
            if($this->is_cname){
                return $this->endpoint;
            }else{
                return implode('.', [$this->bucket, $this->endpoint]);
            }
        }
    }

    protected function buildFilePath($path){
        return trim(
            implode(DIRECTORY_SEPARATOR, [
                $this->_base,
                $path
            ]),
            DIRECTORY_SEPARATOR
        );
    }
    public function setBase($value){
        $this->_base = rtrim($value, DIRECTORY_SEPARATOR);
    }
    public function getBase(){
        return $this->_base;
    }
    public function setIs_cname($value){
        $this->_is_cname = (bool)$value;
    }
    public function getIs_cname(){
        return $this->_is_cname;
    }
}
