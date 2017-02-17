<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\user\tables\UserGroup;
use common\models\user\UserModel;
use common\models\user\tables\User;
use common\models\file\FileModel;
use common\models\file\File;


/**
 *
 */
class InstallController extends Controller{

    public function actionIndex(){
        try {
            $this->actionRbacData();
            $this->actionUserData();
            $this->actionFileData();
            echo ":)\n";
        } catch (\Exception $e) {
            printf($e->getMessage() . "\n");
        }

    }
    public function actionRbacData(){
        $this->installRbacData();
    }
    public function actionUserData(){
        $this->installUserGroupData();
        $this->installUserAssign();
        $this->installUserData();
    }
    public function actionFileData(){
        $this->installFileThumbData();
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

    private function installUserData(){
        printf("\nnow installing user data...\n");
        // Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
        Yii::$app->db->createCommand()->truncateTable('dull_user')->execute();
        // Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
        Yii::$app->db->createCommand()->truncateTable('dull_user_identity')->execute();
        $userData = [
            'User' => [
                'u_username' => 'admin',
                'password' => '123456',
                'password_confirm' => '123456',
                'u_status' => User::STATUS_ACTIVE,
                'u_auth_status' => User::STATUS_AUTHED,
                'u_email' => '784248377@qq.com'
            ],
            'UserIdentity' => [
                'ui_g_name' => UserGroup::ROOT_GROUP,
            ]
        ];
        $userModel = new UserModel();
        $result = $userModel->createUser($userData, true);
        if(!$result){
            list($code, $error) = $userModel->getOneError();
            throw new \Exception(sprintf("%s, %s", $code, $error));
        }
    }


    private function installFileThumbData(){
        printf("\n now install file thumb data... \n");
        $data = require(Yii::getAlias('@app/initdata/image/file-thumbs.php'));
        $fileModel = new FileModel();
        FileModel::deleteFiles(['f_category' => 'file_thumbs']);
        $fileData = [
            'source_path_type' => File::SP_LOCAL,
            'f_storage_type' => File::DR_DISK,
            'f_acl_type' => File::FILE_ACL_PUB_RW,
            'f_category' => 'file_thumbs',
            'save_asyc' => false
        ];
        foreach($data as $item){
            $fileData['source_path'] = $item['source_path'];
            $fileData['f_name'] = $item['f_name'];
            $result = $fileModel->saveFile($fileData);
            if(!$result){
                list($code, $error) = $fileModel->getOneError();
                throw new \Exception("{$code}:{$error}");
            }else{
                echo "installed file thumb image {$fileData['f_name']}\n";
            }
        }

    }

}
