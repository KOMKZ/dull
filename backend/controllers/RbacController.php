<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController;
use common\models\rbac\RbacModel;
use common\models\rbac\tables\AuthItem;

class RbacController extends AdminController
{

    public function actionIndex()
    {
        $rbacModel = new RbacModel();
        list($provider, $pagination) = $rbacModel->getPermissionProvider();
        return $this->render('permission-list', [
            'provider' => $provider
        ]);
    }

    public function actionRoles(){
        $rbacModel = new RbacModel();
        list($provider, $pagination) = $rbacModel->getRoleProvider();
        return $this->render('role-list', [
            'provider' => $provider
        ]);
    }

    public function actionRoleView($name){
        $rbacModel = new RbacModel();
        $role = $rbacModel->getRole(['name' => $name]);
        if(!$role){
            return $this->error(null, Yii::t('app', '数据不存在'));
        }
        $apiurl = Yii::$app->apiurl;
        return $this->render('role-view', [
            'model' => $role,
            'itemTypeMap' => AuthItem::getValidConsts('type'),
            'permissions' => $rbacModel->getPermissions(),
            'assignPermissions' => $rbacModel->getPermissionsByRole($role['name']),
            'permissionAdminUrl' => $apiurl->createAbsoluteUrl(['rbac/update-role-permission']),
            'deleteRoleUrl' => ''
        ]);
    }


}
