<?php
namespace common\models\user;

use Yii;
use common\base\Model;
use common\models\user\tables\User;
use common\models\email\EmailModel;

/**
 *
 */
class UserModel extends Model
{



    public function getOne($condition){
        if(is_object($condition)){
            return $condition;
        }
        if(!$condition){
            return User::find()->where($condition)->one();
        }else{
            return null;
        }
    }
    public function updateUserAuthed($data){
        $user = $this->getOne($data);
        if(!$user){
            $this->addError('', Yii::t('app', '用户不存在'));
            return false;
        }
        $user->u_auth_status = User::STATUS_AUTHED;
        $user->u_auth_key = '';
        if(false !== $user->update(false)){
            return $user;
        }
        return false;
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
            $this->addError('', Yii::t('app', '用户不存在或已经已经激活'));
            return false;
        }
        if($token != $user->u_auth_key){
            $this->addError('', Yii::t('app', '用户不存在token失效'));
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
                'img01' => '/home/kitral/Pictures/04.png',
            ],
            'params' => [
                'username' => '784248377@qq.com',
                'auth_url' => $authUrl,
            ]
        ];
        $emailModel->sendEmail($mail, false);
    }

    protected function buildPasswordHash($password){
        return Yii::$app->security->generatePasswordHash($password);;
    }

    protected function generateAuthKey(){
        return "abcefg:".Yii::$app->security->generateRandomString();
    }





}