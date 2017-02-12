<?php
namespace common\models\user;

use Yii;
use common\base\Model;
use common\models\user\tables\User;
use common\models\email\EmailModel;
use yii\data\ActiveDataProvider;

/**
 *
 */
class UserModel extends Model
{
    public function getProvider($condition = [], $sortData = [], $withPage = true){
        $query = User::find();
        $query = $this->buildQueryWithCondition($query, $condition);

        $defaultOrder = [
            'u_created_at' => SORT_DESC
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
                'attributes' => ['u_created_at'],
                'defaultOrder' => $defaultOrder
            ]
        ]);
        $pagination = $provider->getPagination();
        return [$provider, $pagination];
    }

    public function getOne($condition){
        if(is_object($condition)){
            return $condition;
        }
        if($condition){
            return User::find()->where($condition)->one();
        }else{
            return null;
        }
    }

    public function updateUserAuthed($data){
        $user = $this->getOne($data);
        if(!$user){
            $this->addError('', Yii::t('app', '用户数据不存在'));
            return false;
        }
        $user->u_auth_status = User::STATUS_AUTHED;
        $user->u_auth_key = '';
        if(false !== $user->update(false)){
            return $user;
        }
        return false;
    }
    public function updateAllStatus($condition, $status){
        if(!in_array($status, User::getValidConsts('u_status', true))){
            $this->addError('', 'u_status值不合法');
            return false;
        }
        $this->updateAllUser($condition, ['u_status' => $status]);
        return true;
    }
    protected function updateAllUser($condition, $data, $parmas = []){
        $data['u_updated_at'] = time();
        User::updateAll($data, $condition, $parmas);
    }
    public function updateUser($condition, $data){
        $user = $this->getOne($condition);
        if(!$user){
            $this->addError('', Yii::t('app', '用户数据不存在'));
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user->scenario = 'update';
            if($user->load($data) && $user->validate()){
                if($user->isNoAuth){
                    $user->u_auth_key = $this->generateAuthKey();
                }
                if(!empty($user->password)){
                    $user->u_password_hash = $this->buildPasswordHash($user->password);
                }
                $result = $user->update(false);
                if(false === $result){
                    $this->addError('', Yii::t('app', '数据写入失败'));
                    return false;
                }
                if($user->isNoAuth){
                    $this->sendAuthEmailToUser($user);
                }
                $transaction->commit();
                return $user;
            }else{
                $this->addErrors($user->getErrors());
                return false;
            }
        } catch (\Exception $e) {
            $transaction->rollback();
            Yii::error($e);
            $this->addError('', Yii::t('app', '发生异常'));
            return false;
        }
    }
    public function createUser($data, $user = null){
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = !$user ? new User() : $user;
            $user->scenario = 'create';
            if($user->load($data) && $user->validate()){
                if($user->isNoAuth){
                    $user->u_auth_key = $this->generateAuthKey();
                }
                $user->u_password_hash = $this->buildPasswordHash($user->password);
                $result = $user->insert(false);
                if(!$result){
                    $this->addError('', Yii::t('app', '数据写入失败'));
                    return false;
                }
                if($user->isNoAuth){
                    $this->sendAuthEmailToUser($user);
                }
                $transaction->commit();
                return $user;
            }else{
                $this->addErrors($user->getErrors());
                return false;
            }
        } catch (\Exception $e) {
            $transaction->rollback();
            Yii::error($e);
            $this->addError('', Yii::t('app', '发生异常'));
            return false;
        }
    }
    public static function signData($data){
        ksort($data);
        $key = 'philips';
        return sha1(self::dataToString($data).$key);
    }
    protected static function dataToString($data){
        $string = '';
        foreach($data as $k => $v){
            if(!is_array($v)){
                $string .= $k . $v;
            }
        }
        return $string;
    }
    public static function validateSign($data, $target){
        $source = self::signData($data);
        return $source == $target;
    }

    public function validateSignUpAuthData($uid, $token, $expire, $sign){
        $data = [
            'id' => $uid,
            'token' => $token,
            'expire' => $expire
        ];
        $isValid = self::validateSign($data, $sign);
        if(!$isValid){
            $this->addError('', Yii::t('app', '访问禁止'));
            return false;
        }
        if(time() > $expire){
            $this->addError('', Yii::t('app', '已经过期'));
            return false;
        }
        $user = $this->getOne(['u_id' => $uid, 'u_auth_status' => User::STATUS_NO_AUTH]);
        if(!$user){
            $this->addError('', Yii::t('app', '用户数据不存在或已经已经激活'));
            return false;
        }
        if($token != $user->u_auth_key){
            $this->addError('', Yii::t('app', '用户数据不存在token失效'));
            return false;
        }
        return true;
    }

    protected function sendAuthEmailToUser($user){
        $emailModel = new EmailModel();
        $query = [
            'token' => $user->u_auth_key,
            'id' => $user->u_id,
            'expire' => time() + 3600
        ];
        $signString = self::signData($query);
        $query['sign'] = $signString;
        array_unshift($query, 'user/signup-auth');
        $authUrl = Yii::$app->urlbuilder->createAbsoluteUrl($query, 'http');
        $mail = [
            'subject' => Yii::t('app', 'kitralzhong注册认证邮件'),
            'to' => $user->u_email,
            'template' => 'signup-user-auth-email',
            'body' => ['','text/html'],
            'img' => [
                'img01' => '/home/kitral/Pictures/3.png',
            ],
            'params' => [
                'username' => '784248377@qq.com',
                'auth_url' => $authUrl,
            ]
        ];
        if(!$emailModel->sendEmail($mail, true)){
            // 12 todo
            $emailModel::insertFailedEmail($mail, 12, $emailModel->getErrors());
        }
    }

    protected function buildPasswordHash($password){
        return Yii::$app->security->generatePasswordHash($password);;
    }

    protected function generateAuthKey(){
        return Yii::$app->security->generateRandomString();
    }





}
