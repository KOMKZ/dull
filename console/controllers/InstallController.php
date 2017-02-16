<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\user\tables\UserGroup;

/**
 *
 */
class InstallController extends Controller{

    public function actionIndex(){
        $this->actionRbacData();
        $this->actionUserData();
    }
    public function actionRbacData(){
        $this->installRbacData();
    }
    public function actionUserData(){
        $this->installUserGroupData();
        $this->installUserAssign();
    }

    private function installRbacData(){
        printf("\nnow installing permission....\n\n");
        $rbacInitData = require(Yii::getAlias('@app/initdata/rbac/permission-data.php'));
        // 卸载原来的权限表
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
        Yii::$app->db->createCommand()->truncateTable('dull_auth_item')->execute();
        Yii::$app->db->createCommand()->truncateTable('dull_auth_item_child')->execute();
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 1;')->execute();
        //
        $auth = Yii::$app->authManager;

        foreach($rbacInitData as $item){
            $superPermission = $auth->createPermission($item['name']);
            $superPermission->description = $item['description'];
            $auth->add($superPermission);
            printf("insert permission %s\n", $item['name']);
            if(array_key_exists('children', $item)){
                foreach($item['children'] as $subItem){
                    $permission = $auth->createPermission($subItem['name']);
                    $permission->description = $subItem['description'];
                    $auth->add($permission);
                    printf("insert permission %s\n", $subItem['name']);
                    $auth->addChild($superPermission, $permission);
                    printf("add `%s` as child of `%s`\n", $subItem['name'], $item['name']);
                }
            }
        }


        printf("\nnow installing role....\n\n");
        $roleFile = Yii::getAlias('@app/initdata/rbac/role-data.php');
        $roles = require($roleFile);
        $auth = Yii::$app->authManager;
        foreach($roles as $roleDef){
            $role = $auth->createRole($roleDef['name']);
            $role->description = $roleDef['description'];
            $auth->add($role);
            printf("insert role `%s`\n", $roleDef['name']);

            foreach($roleDef['permissions'] as $parent => $perm){
                if(in_array('*', $perm)){
                    $permission = $auth->getPermission($parent);
                    $auth->addChild($role, $permission);
                    printf("insert all permissons of `%s` to the role `%s`\n", $parent, $roleDef['name']);
                }else{
                    foreach($perm as $permName){
                        $permisson = $auth->getPermission($permName);
                        $auth->addChild($role, $permisson);
                        printf("insert permisson `%s` to the role `%s`\n", $permName, $roleDef['name']);
                    }
                }
            }
        }
    }

    private function installUserGroupData(){
        printf("\nnow installing user group data....\n\n");
        $userGroupFile = Yii::getAlias('@app/initdata/user/user-group-data.php');
        $userGroup = require($userGroupFile);
        $tableName = UserGroup::tableName();
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
        Yii::$app->db->createCommand()->truncateTable($tableName)->execute();
        $command = Yii::$app->db->createCommand()->batchInsert($tableName, ['ug_name', 'ug_description', 'ug_created_at', 'ug_updated_at'], $userGroup);
        $result = $command->execute();
        printf("installed {$result} user group item.\n");
    }

    private function installUserAssign(){
        printf("\nnow installing role assign....\n\n");
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
        Yii::$app->db->createCommand()->truncateTable('dull_auth_assignment')->execute();
        $assignFile = Yii::getAlias('@app/initdata/rbac/assign-data.php');
        $assignData = require($assignFile);
        $auth = Yii::$app->authManager;
        foreach($assignData as $name => $roles){
            foreach($roles as $roleName){
                $role = $auth->getRole($roleName);
                $auth->assign($role, $name);
                printf("assign role `%s` to object id `%s`\n", $roleName, $name);
            }
        }
    }
}
