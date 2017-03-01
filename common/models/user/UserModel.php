<?php
namespace common\models\user;

use Yii;
use common\base\Model;
use common\models\user\tables\User;
use common\models\user\tables\UserFocus;
use common\models\user\tables\UserIdentity;
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
    public function login($condition, $password, $remember = false){
        $remember = (bool)$remember;
        $condition['u_status'] = User::STATUS_ACTIVE;
        $condition['u_auth_status'] = User::STATUS_AUTHED;
        $user = $this->getOne($condition);
        if(!$user){
            $this->addError('', Yii::t('app', '用户不存在'));
            return false;
        }
        $result = $this->validatePassword($user, $password);
        if(!$result){
            $this->addError('', Yii::t('app', '密码错误'));
            return false;
        }
        Yii::$app->user->login($user, $remember ? 3600 * 24 * 30 : 0);
        return true;
    }

    public function validatePassword($condition, $password)
    {
        $user = $this->getOne($condition);
        if(!$user){
            return false;
        }
        if(!Yii::$app->security->validatePassword($password, $user->u_password_hash)){
            return false;
        }
        return $user;
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

    public function updateUser($condition, $data, $force = false){
        $user = $this->getOne($condition);
        if(!$user){
            $this->addError('', Yii::t('app', '用户数据不存在'));
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 更新基本信息
            $user->scenario = 'update';
            if(!$user->load($data) || !$user->validate()){
                $this->addError('', $this->getArErrMsg($user));
                return false;
            }
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
            // 更新身份信息
            $userIdentity = $user->identity;
            if(!$force && $userIdentity->isSuperRoot){
                $this->addError('', Yii::t('app', '该用户禁止修改'));
                return false;
            }
            if(!$userIdentity->load($data) || !$userIdentity->validate()){
                $this->addError('', $this->getArErrMsg($userIdentity));
                return false;
            }

            $result = $userIdentity->update(false);
            if(false === $result){
                $this->addError('', Yii::t('app', '数据写入失败'));
                return false;
            }

            if($user->isNoAuth){
                $this->sendAuthEmailToUser($user);
            }

            $transaction->commit();
            return $user;
        } catch (\Exception $e) {
            $transaction->rollback();
            Yii::error($e);
            $this->addError('', Yii::t('app', '发生异常'));
            return false;
        }
    }

    public function createUser($data, $force = false){
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 验证
            $userIdentity = new UserIdentity();
            $userIdentity->scenario = 'create';
            if(!$userIdentity->load($data) || !$userIdentity->validate()){
                $this->addError('', $this->getArErrMsg($userIdentity));
                return false;
            }
            if(!$force && $userIdentity->isSuperRoot){
                $this->addError('', Yii::t('app', '禁止创建该保留组用户'));
                return false;
            }

            // 加入基础信息
            $user = new User();
            $user->scenario = 'create';
            if(!$user->load($data) || !$user->validate()){
                $this->addError('', $this->getArErrMsg($user));
                return false;
            }
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
            // 加入身份信息
            $userIdentity->ui_uid = $user->u_id;
            $result = $userIdentity->insert(false);
            if(!$result){
                $this->addError('', Yii::t('app', '数据写入失败'));
                return false;
            }

            // 加入关注者
            if(1 != $user->u_id){
                $this->addUserUFocus($user->u_id, [1]);
            }

            $transaction->commit();
            return $user;
        } catch (\Exception $e) {
            $transaction->rollback();
            Yii::error($e);
            $this->addError('', Yii::t('app', '发生异常'));
            return false;
        }
    }

    public function addUserUFocus($uid, $fuids = [], $strict = true){
        $master = User::find()->where(['u_id' => $uid])->asArray()->one();
        if(!$master){
            $this->addError("", "{$uid}不存在");
            return false;
        }
        $validFuids = array_keys(User::find()
                                  ->select('u_id')
                                  ->where(['in', 'u_id', $fuids])
                                  ->asArray()
                                  ->indexBy('u_id')
                                  ->all()
                                  );

        if($strict && !empty($invalidFuids = array_diff_assoc($fuids, $validFuids))){
            $this->addError('', sprintf('用户id不存在：%s', implode(', ', $invalidFuids)));
            return false;
        }
        $query =      UserFocus::find()->select('uf_f_uid')
                                       ->where(['in', 'uf_f_uid', $fuids]);
        $focusFuids = array_keys($query->andWhere(['=', 'uf_uid', $uid])
                                       ->asArray()
                                       ->indexBy('uf_f_uid')
                                       ->all());

        $fuids = array_diff_assoc($validFuids, $focusFuids);
        if(!empty($fuids)){
            $focusData = [];
            foreach($fuids as $fuid){
                $focusData[] = ['uf_uid' => $uid, 'uf_f_uid' => $fuid, 'uf_created_at' => time()];
            }
            $cmd = Yii::$app->db->createCommand()->batchInsert(UserFocus::tableName(), ['uf_uid', 'uf_f_uid', 'uf_created_at'], $focusData);
            $cmd->execute();
            return true;
        }
        return true;
    }

    public function removeUserUFocus($uid, $fuids = []){
        $master = User::find()->where(['u_id' => $uid])->asArray()->one();
        if(!$master){
            $this->addError("", "{$uid}不存在");
            return false;
        }
        UserFocus::deleteAll(['and', ['=', 'uf_uid', $uid], ['in', 'uf_f_uid', $fuids]]);
        return true;
    }

    public function getUserUFocus($uid, $withPage = true){
       $u = User::tableName();
       $uf = UserFocus::tableName();
       $query = UserFocus::find()
                           ->select('
                           uf.*,
                           u.u_username,
                           u_f.u_username as u_f_username
                           ')
                           ->from(sprintf('(%s as uf)', UserFocus::tableName()))
                           ->leftJoin(sprintf('(%s as u)', User::tableName()), 'uf.uf_uid = u.u_id')
                           ->leftJoin(sprintf('(%s as u_f)', User::tableName()), 'uf.uf_f_uid = u_f.u_id')
                           ->where(['uf.uf_uid' => $uid])
                           ->asArray();
       if(!$withPage){
           $pageConfig['pageSize'] = 0;
       }else{
           $pageConfig['pageSize'] = 10;
       }
       $provider = new ActiveDataProvider([
           'query' => $query
       ]);
       $pagination = $provider->getPagination();
       return [$provider, $pagination];
    }

    public function getUserUFans($uid){
        $u = User::tableName();
        $uf = UserFocus::tableName();
        $query = UserFocus::find()
                            ->select('
                            uf.*,
                            u.u_username,
                            u_f.u_username as u_f_username
                            ')
                            ->from(sprintf('(%s as uf)', UserFocus::tableName()))
                            ->leftJoin(sprintf('(%s as u)', User::tableName()), 'uf.uf_uid = u.u_id')
                            ->leftJoin(sprintf('(%s as u_f)', User::tableName()), 'uf.uf_f_uid = u_f.u_id')
                            ->where(['uf.uf_f_uid' => $uid])
                            ->asArray();
        $provider = new ActiveDataProvider([
            'query' => $query
        ]);
        $pagination = $provider->getPagination();
        return [$provider, $pagination];
    }

    public function hadOneFocus($uid, $fuid){
        $uf = UserFocus::tableName();
        $sql = "
            select 1 from $uf where uf_uid = :p1 and uf_f_uid = :p2
        ";
        $result = Yii::$app->db->createCommand($sql,[':p1' => $uid, ':p2' => $fuid])->queryOne();
        return !!$result;
    }

    public function hadOneFan($uid, $fuid){
        $uf = UserFocus::tableName();
        $sql = "
            select 1 from $uf where uf_uid = :p1 and uf_f_uid = :p2
        ";
        $result = Yii::$app->db->createCommand($sql,[':p1' => $fuid, ':p2' => $uid])->queryOne();
        return !!$result;
    }

    protected static function getUserFocusTableName(){
        return "{{%user_focus}}";
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
        $authUrl = Yii::$app->frurl->createAbsoluteUrl($query, 'http');
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
