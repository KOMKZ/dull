<?php
namespace common\models\file;

use common\base\Model;
use common\models\file\File;
use common\models\file\DiskDriver;

/**
 *
 */
class FileModel extends Model
{
    CONST DR_DISK = 'disk';

    public function saveFile(File $file){

        $driver = $this->instanceDriver($file->driverType);
        $file = $driver->saveFile($file);

    }

    public function newFile(){
        return new File();
    }

    protected function instanceDriver($type){
        switch ($type) {
            case self::DR_DISK:
                return new DiskDriver();
            default:
                throw new \Exception("zh_exec:不支持存储类型{$type}");
                break;
        }
    }
}
