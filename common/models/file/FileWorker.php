<?php
namespace common\models\file;

use Yii;
use common\models\file\File;
use common\models\file\FileModel;
use yii\db\Query;

class FileWorker
{
    static private $fileModel;
    static public $workerCount = 5;



    public static function handleFile($msg){
        try {
            $data = json_decode($msg->body);
            if(!$data){
                return $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            }
            $file = File::findOne($data->f_id);
            if(!$file){
                return $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            }
            if($file->isUploaded){
                return $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            }
            $file->source_path = $data->source_path;
            $file->source_path_type = $data->source_path_type;
            $model = self::getModel();
            $result = $model->uploadFileToTps($file);
            if(!$result){
                $model->setFileFailed($file);
            }else{
                // 保存成功
                $model->setFileUploaded($file);
            }
        } catch (\Exception $e) {
            Yii::error($e);
            echo $e->getMessage();
        }
        return $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

    }

    protected static function getModel(){
        if(!self::$fileModel){
            self::$fileModel = new FileModel();
        }
        return self::$fileModel;
    }

}
