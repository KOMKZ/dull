<?php
namespace api\controllers;

use Yii;
use common\base\ApiController;
use common\models\rbac\RbacModel;

/**
 *
 */
class RbacController extends ApiController
{
    public function actionUpdateRolePermission(){
        $post = Yii::$app->request->post();
        $rbacModel = new RbacModel();
        if(empty($post['role_name'])){
            $post['role_name'] = '';
        }
        if(empty($post['new_items'])){
            $post['new_items'] = [];
        }
        if(empty($post['rm_items'])){
            $post['rm_items'] = [];
        }


        $result = $rbacModel->updateRolePermission($post['role_name'], $post['new_items'], $post['rm_items']);
        if(!$result){
            list($code, $error) = $rbacModel->getOneError();
            return $this->error($code, $error);
        }
        return $this->succ();
    }

    public function actionAssignAdmin(){
        $post = Yii::$app->request->post();
        $rbacModel = new RbacModel();
        if(empty($post['assign_id'])){
            $post['assign_id'] = '';
        }
        if(empty($post['new_items'])){
            $post['new_items'] = [];
        }
        if(empty($post['rm_items'])){
            $post['rm_items'] = [];
        }
        $result = $rbacModel->updateAssignRole($post['assign_id'], $post['new_items'], $post['rm_items']);
        if(!$result){
            list($code, $error) = $rbacModel->getOneError();
            return $this->error($code, $error);
        }
        return $this->succ();

    }
}
