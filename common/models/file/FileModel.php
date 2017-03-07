<?php
namespace common\models\file;

use Yii;
use common\base\Model;
use common\models\file\File;
use common\models\file\DiskDriver;
use yii\web\HttpException;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use yii\data\ActiveDataProvider;


/**
 *
 */
class FileModel extends Model
{
    static private $amqpConn;
    static private $Channel;

    protected function getValidPrefix(){
        return [
            'trainor-oss-test.oss-cn-shenzhen.aliyuncs.com'
        ];
    }
    protected function buildIdFromUrl($url){
        return 'oss:' . basename($url);
    }
    protected function parseIdFromUrls($urls){
        $prefix = $this->getValidPrefix();
        $result = [];
        foreach($urls as $url){
            $one = parse_url($url);
            if(in_array($one['host'], $prefix)){
                $result[] = $this->buildIdFromUrl($url);
            }
        }
        return array_unique($result);;
    }
    protected function getUrlFromContent($content){
        if(preg_match_all('/(http:\/\/.*?)[\s\"\'\n]+/', $content, $matches)){
            return $matches[1];
        }else{
            return [];
        }
    }

    public function setFileValidFromArray($newIds, $oldIds = []){
        // 得到删除的id
        $deleteIds = array_diff_assoc($oldIds, $newIds);
        $newValidIds = array_diff_assoc($newIds, $oldIds);
        if(!empty($newValidIds)){
            // 设置文件为有效
        }
        if(!empty($deleteIds)){
            // 设置文件为临时文件
        }
    }

    public function setFileValidFromContent($newContent, $oldContent = null){
        $newIds = $this->parseIdFromUrls($this->getUrlFromContent($newContent));
        if(!empty($newIds)){
            if(!empty($oldContent)){
                $oldIds = $this->parseIdFromUrls($this->getUrlFromContent($oldContent));
            }else{
                $oldIds = [];
            }
            // 得到删除的id
            $deleteIds = array_diff_assoc($oldIds, $newIds);
            $newValidIds = array_diff_assoc($newIds, $oldIds);
            if(!empty($newValidIds)){
                // 设置文件为有效
            }
            if(!empty($deleteIds)){
                // 设置文件为临时文件
            }
        }
        return 0;
    }



    public function getProvider($condition = [], $sortData = [], $withPage = true){
        $query = File::find();
        $query = $this->buildQueryWithCondition($query, $condition);

        $defaultOrder = [
            'f_created_at' => SORT_DESC
        ];

        if(!empty($sortData)){
            $defaultOrder = $sortData;
        }
        $pageConfig = [];
        if(!$withPage){
            $pageConfig['pageSize'] = 0;
        }else{
            $pageConfig['pageSize'] = 20;
        }
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pageConfig,
            'sort' => [
                'attributes' => ['f_created_at'],
                'defaultOrder' => $defaultOrder
            ]
        ]);
        $pagination = $provider->getPagination();
        return [$provider, $pagination];
    }

    public function getOne($condition){
        if(!empty($condition)){
            return File::find()->where($condition)->one();
        }else{
            return null;
        }
    }

    public function getFileUrl($id){
        if(!($id instanceof File)){
            $file = $this->getOne(['f_id' => $id]);
        }else{
            $file = $id;
        }
        if(!$file){
            return null;
        }
        $driver = $this->instanceDriver($file->f_storage_type);
        $url = $driver->getFileUrl($file->getFilePath(), $file->f_host, $file->isPublic);
        return $url;
    }

    public function getLocalFileUrl($name){
        $driver = $this->instanceDriver(File::DR_DISK);
        return $driver->getFileUrl($name);
    }

    public function getLocalFilePath($name){
        $driver = $this->instanceDriver(File::DR_DISK);
        return $driver->getFilePath($name);
    }

    public function output($id){
        if(!($id instanceof File)){
            $file = $this->getOne(['f_id' => $id]);
        }else{
            $file = $id;
        }
        if(!$file){
            throw new HttpException(404, Yii::t('app', '数据不存在'));
        }
        $driver = $this->instanceDriver($file->f_storage_type);
        $driver->output($file);
    }

    public function setFileUploaded($file){
        $file->f_status = File::STATUS_UPLOADED;
        if(false === $file->update(false)){
            $this->addError('', Yii::t('app', '数据库更新失败'));
            return false;
        }
        return $file;
    }

    public function setFileFailed($file){
        $file->f_status = File::STATU_UPLOAD_FAIL;
        if(false === $file->update(false)){
            $this->addError('', Yii::t('app', '数据库更新失败'));
            return false;
        }
        return $file;
    }

    public static function deleteFiles($condition = []){
        File::deleteAll($condition);
    }

    public function saveFile($data, $file = null){
        if(!$file){
            $file = new File();
        }
        $file->scenario = 'create';
        if(!$file->load($data, '') || !$file->validate()){
            $this->addError('', $this->getArErrMsg($file));
            return false;
        }
        if(!$file->save_asyc){
            $file = $this->uploadFileToTps($file);
            $file->f_status = File::STATUS_UPLOADED;
        }else{
            // todo异步保存需要保存本地文件
            $file->f_status = File::STATUS_IN_QUEUE;
        }
        // insert record in db
        $file = $this->saveFileInfoInDb($file);
        if($file->save_asyc){
            $this->sendFileDataAsyc($file);
        }
        return $file;
    }

    public function saveTmpFile($filePath){
        $file = new File();
        $file->f_storage_type = File::DR_DISK;
        $file->source_path = $filePath;
        $file->source_path_type = File::SP_LOCAL;
        $file->f_category = 'tmp_file';
        $path_parts = pathinfo($filePath);
        $file->f_name = $path_parts['filename'];
        return $this->uploadFileToTps($file);
    }


    public function uploadTmpFile($tmpName){
        $path = $this->getLocalFilePath($tmpName);
        if(!file_exists($path)){
            $this->addError('', Yii::t('app', '文件不存在'.$path));
            return false;
        }
        $file = new File();
        $file->source_path = $path;
        $file->source_path_type = File::SP_LOCAL;
        $file->f_storage_type = File::DR_DISK;
        $file->f_acl_type = File::FILE_ACL_PRI;
        $file->f_category = 'post_thumb';
        $file->save_asyc = false;
        $path_parts = pathinfo($tmpName);
        $file->f_name = $path_parts['filename'];

        $result = $this->saveFile(['f_name' => $path_parts['filename']], $file);
        if(!$result){
            return false;
        }
        return $file;
    }

    public function uploadFileToTps($file){
        $driver = $this->instanceDriver($file->f_storage_type);
        // save in storage media
        $file = $driver->save($file);
        if(!$file){
            $this->addErrors($driver->getErrors());
            return false;
        }
        return $file;
    }

    protected function sendFileDataAsyc($file){
        $connection = $this->getAmqpConn();
        $channel = $this->getChannel();
        $msg = new AMQPMessage(json_encode([
            'f_id' => $file->f_id,
            'source_path' => $file->source_path,
            'source_path_type' => $file->source_path_type
        ]), ['delivery_mode' => 2]);
        $channel->basic_publish($msg, '', 'file-job');
    }

    protected function saveFileInfoInDb($file){
        $file->f_size = $file->caculateSize();
        $file->f_mime_type = $file->parseMimeType();
        $file->f_meta_data = json_encode($file->parseMetaData());
        $file->f_created_at = time();
        $file->f_updated_at = time();
        $file->f_depostion_name = $file->buildTotalName();
        if($file->insert(false)){
            return $file;
        }else{
            return false;
        }
    }

    protected function instanceDriver($type){
        switch ($type) {
            case File::DR_DISK:
                return Yii::$app->diskfile;
            case File::DR_OSS:
                return Yii::$app->ossfile;
            default:
                throw new \Exception("zh:不支持存储类型{$type}");
                break;
        }
    }
    public static function getExtImgMap(){
        return [
            'default' => 'txt_30x36.png',
            'pdf' => 'pdf_30x36.png',
            'zip' => 'zip_30x36.png',
            'doc' => 'doc_30x36.png',
            'etc' => 'etc_30x36.png',
            'jpg' => 'jpg_30x36.png',
            'ppt' => 'ppt_30x36.png',
            'wav' => 'wav_30x36.png',
            'video' => 'avi_30x36.png',
            'audio' => 'avi_30x36.png'
        ];
    }
    public function getExtImgUrl($file){
        $exifTool = $file->getMetaObj();
        $ext = $exifTool->getFileExt();
        if(!array_key_exists($ext, self::getExtImgMap())){
            if($exifTool->isVideo()){
                $ext = 'video';
            }elseif($exifTool->isAudio()){
                $ext = 'audio';
            }elseif($exifTool->isMSWord() || $exifTool->isWPSWord()){
                $ext = 'doc';
            }elseif($exifTool->isWPSPpt() || $exifTool->isMSPpt()){
                $ext = 'ppt';
            }else{
                $ext = 'default';
            }
        }
        return $this->getLocalFileUrl("file_thumbs/" . self::getExtImgMap()[$ext]);
    }

    private function getChannel(){
        if(self::$Channel){
            return self::$Channel;
        }
        $conn = $this->getAmqpConn();
        $channel = $conn->channel();
        $channel->queue_declare('file-job', false, true, false, false);
        return self::$Channel = $channel;
    }

    private function getAmqpConn(){
        if(self::$amqpConn){
            return self::$amqpConn;
        }
        return self::$amqpConn = new AMQPStreamConnection('localhost', 5672, 'kitral', 'philips');
    }
}
