<?php
namespace common\models\file;

use Yii;
use common\base\Model;
use common\models\file\File;
use common\models\file\DiskDriver;
use yii\web\HttpException;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


/**
 *
 */
class FileModel extends Model
{
    static private $amqpConn;
    static private $Channel;

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
        $url = $driver->getFileUrl($file);
        return $url;
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

    public function saveFile($data){
        $file = new File;
        $file->scenario = 'create';
        if(!$file->load($data, '') || !$file->validate()){
            $this->addErrors($file->getErrors());
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
