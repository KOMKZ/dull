<?php
namespace common\models\file;

use Yii;
use common\base\Model;
use common\models\file\File;
use common\models\file\DiskDriver;
use yii\web\HttpException;

/**
 *
 */
class FileModel extends Model
{
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
            throw new HttpException(404, Yii::t('app', '文件不存在'));
        }
        $driver = $this->instanceDriver($file->f_storage_type);
        $driver->output($file);
    }

    public function saveFile($data){
        $file = new File;
        $file->scenario = 'create';
        if(!$file->load($data, '') || !$file->validate()){
            $this->addErrors($file->getErrors());
            return false;
        }
        $driver = $this->instanceDriver($file->f_storage_type);
        // save in storage media
        $file = $driver->save($file);
        if(!$file){
            $this->addErrors($driver->getErrors());
            return false;
        }
        // insert record in db
        $file = $this->saveFileInfoInDb($file);
        return $file;
    }

    protected function saveFileInfoInDb($file){
        $file->f_size = $file->caculateSize();
        $file->f_mime_type = $file->parseMimeType();
        $file->f_meta_data = json_encode($file->parseMetaData());
        $file->f_created_at = time();
        $file->f_updated_at = time();
        $file->f_depostion_name = $file->buildTotalName();
        return $file->insert(false);
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
}
