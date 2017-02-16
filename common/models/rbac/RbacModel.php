<?php
namespace common\models\rbac;

use Yii;
use common\base\Model;
use common\models\rbac\tables\AuthItem;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 *
 */
class RbacModel extends Model
{
    public function updateRolePermission($roleName, $new_items = [], $rm_items = []){
        $role = Yii::$app->authManager->getRole($roleName);
        if(!$role){
            $this->addError('', Yii::t('app', '数据不存在'));
            return false;
        }
        if($new_items){
            foreach($new_items as $pmiName){
                $permission = Yii::$app->authManager->getPermission($pmiName);
                $result = Yii::$app->authManager->addChild($role, $permission);
            }
        }
        if($rm_items){
            foreach($rm_items as $pmiName){
                $permission = Yii::$app->authManager->getPermission($pmiName);
                $result = Yii::$app->authManager->removeChild($role, $permission);
            }
        }
        return true;
    }

    public function updateAssignRole($ai, $newItems = [], $rmItems){
        if($newItems){
            foreach($newItems as $roleName){
                $role = Yii::$app->authManager->getRole($roleName);
                Yii::$app->authManager->assign($role, $ai);
            }
        }
        if($rmItems){
            foreach($rmItems as $roleName){
                $role = Yii::$app->authManager->getRole($roleName);
                Yii::$app->authManager->revoke($role, $ai);
            }
        }
        return true;
    }

    public function getRole($condition){
        if(is_object($condition)){
            return $condition;
        }
        if($condition){
            $condition['type'] = AuthItem::TYPE_ROLE;
            return AuthItem::find()->where($condition)->one();
        }else{
            return null;
        }
    }
    public function getRoles(){
        $result = Yii::$app->authManager->getRoles();
        return ArrayHelper::toArray($result);
    }
    public function getRolesByAi($assignId){
        $result = Yii::$app->authManager->getRolesByUser($assignId);
        return ArrayHelper::toArray($result);
    }

    public function getPermissionsByRole($name){
        $result = Yii::$app->authManager->getPermissionsByRole($name);
        return ArrayHelper::toArray($result);
    }
    public function getPermission($condition){
        if(is_object($condition)){
            return $condition;
        }
        if($condition){
            $condition['type'] = AuthItem::TYPE_PERM;
            return AuthItem::find()->where($condition)->one();
        }else{
            return null;
        }
    }
    public function getPermissions(){
        $result = Yii::$app->authManager->getPermissions();
        return ArrayHelper::toArray($result);
    }

    protected function getItemProvider($type, $condition = [], $sortData = [], $withPage = true){
        $query = AuthItem::find();
        $query = $this->buildQueryWithCondition($query, $condition);
        $query->andWhere(['=', 'type', $type]);

        $defaultOrder = [
            'created_at' => SORT_DESC
        ];

        if(!empty($sortData)){
            $defaultOrder = $sortData;
        }
        $pageConfig = [];
        if(!$withPage){
            $pageConfig['pageSize'] = 0;
        }else{
            $pageConfig['pageSize'] = 10;
        }
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pageConfig,
            'sort' => [
                'attributes' => ['created_at'],
                'defaultOrder' => $defaultOrder
            ]
        ]);
        $pagination = $provider->getPagination();
        return [$provider, $pagination];
    }
    public function getPermissionProvider($condition = [], $sortData = [], $withPage = true){
        return $this->getItemProvider(AuthItem::TYPE_PERM, $condition, $sortData, $withPage);
    }
    public function getRoleProvider($condition = [], $sortData = [], $withPage = true){
        return $this->getItemProvider(AuthItem::TYPE_ROLE, $condition, $sortData, $withPage);
    }
}
