<?php
namespace common\models\file;

use common\base\Model;

/**
 *
 */
class File extends Model
{
    const PATH_TYPE_LOCAL = 'local';

    private $_driverType;
    private $_sourcePath;


    public function rules(){
        return [
            [
                [
                    'sourcePath',
                    'driverType',
                    'pathType',
                ], 'safe'
            ]
        ];
    }

    public function setPathType($value){

    }

    public function setSourcePath($path){
        if(!file_exists($path)){
            $this->addError('', "{$path} 指定的路径不存在");
            return false;
        }
        $this->_sourcePath = $path;
    }
    public function getSourcePath(){
        return $this->_sourcePath;
    }

    public function getDriverType(){

    }
    public function setDriverType(){

    }

}
