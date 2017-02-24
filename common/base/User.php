<?php
namespace common\base;

use Yii;
use yii\web\User as BaseUser;

/**
 *
 */
class User extends BaseUser
{
    private $_access = [];
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        if ($allowCaching && empty($params) && isset($this->_access[$permissionName])) {
            return $this->_access[$permissionName];
        }
        if (($accessChecker = $this->getAccessChecker()) === null) {
            return false;
        }
        $assignId = null;
        if($this->identity){
            $assignId = $this->identity->identity->ui_gid;
        }
        $permissionName = Yii::$app->id . '-' . $permissionName;
        $access = $accessChecker->checkAccess($assignId, $permissionName, $params);
        if ($allowCaching && empty($params)) {
            $this->_access[$permissionName] = $access;
        }
        return $access;
    }
}
