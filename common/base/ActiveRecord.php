<?php
namespace common\base;

use yii\db\ActiveRecord as Base;

/**
 *
 */
class ActiveRecord extends Base
{
    protected $_logicModel = null;

    public static function logicModelName(){
        throw new \Exception("必须定logicModelName");
    }

    public function getLogicModel(){
        if(!is_object($this->_logicModel)){
            $className = static::logicModelName();
            $this->_logicModel = new $className();
        }
        return $this->_logicModel;
    }
}
