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
    public $ossFirst = true;

    public function getFileUrl($queryId, $host = '', $isPublic = true){
        list($saveType, $path) = FileModel::parseQueryId($queryId);
        if($this->ossFirst){
            return $this->getFileDirectUrl($queryId, $host = '', $isPublic);
        }else{
            // todo 签名
            return Yii::$app->frurl->createAbsoluteUrl(['file/read', 'name' => $queryId], 'http');
        }
    }
    public function getFileDirectUrl($queryId, $host = '', $isPublic = true){
        list($saveType, $path) = FileModel::parseQueryId($queryId);
        if($isPublic){
            $host = $this->getHostName();
            $objectId = $this->buildFileObjectId($queryId);
            return "http://" . implode('/', [$host, $objectId]);
        }else{
            $objectId = $this->buildFileObjectId($queryId);
            return $this->instanceOss()->signUrl($this->bucket, $objectId, 3600 , Oss::OSS_HTTP_GET, [
                Oss::OSS_HEADERS => [
                    Oss::OSS_CONTENT_DISPOSTION => '我爱你.pdf'
                ]
            ]);
        }
    }
    public function outputByQid($qid, $isPublic = true){
        $url = $this->getFileDirectUrl($qid, $isPublic);
        header("Location: {$url}");
        exit();
    }
    public function buildFileObjectId($queryId){
        return $this->_base . DIRECTORY_SEPARATOR . FileModel::coverQidToPath($queryId);
    }
    public function deleteFile($qid, $bucket = ''){
        $object = $this->_base . DIRECTORY_SEPARATOR . FileModel::coverQidToPath($qid);
        $bucket = $bucket ? $bucket : $this->bucket;
        return $this->instanceOss(true)->deleteObject($bucket, $object, $options = NULL);
    }
    public function save(File $file){
        $objectName = $file->getFileSavePath();
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
    public function instanceOss($inner = false){
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
    public function buildSaveInfo($file){
        $file->setSaveDir($this->_base);
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
