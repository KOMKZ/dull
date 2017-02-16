<?php
namespace common\models\user\tables;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use common\models\user\UserModel;
use common\models\user\tables\UserIdentity;



/**
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
    private static $_constMap = [];
    const STATUS_ACTIVE = 'active';
    const STATUS_LOCKED = 'locked';
    const STATUS_DELETE = 'delete';

    const STATUS_NO_AUTH = 0;
    const STATUS_AUTHED = 1;

    public $password;
    public $password_confirm;
    public $remember;
    private $_login_id;

    public function getIdentity(){
        return $this->hasOne(UserIdentity::className(), ['ui_uid' => 'u_id'])->one();
    }

    public function getLogin_id(){
        return $this->_login_id;
    }

    public function setLogin_id($value){
        $this->_login_id = $value;
    }

    public static function findIdentity($id)
    {
        return static::findOne(['u_id' => $id, 'u_status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findByUsername($username)
    {
        return static::findOne(['u_username' => $username, 'u_status' => self::STATUS_ACTIVE]);
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'u_password_reset_token' => $token,
            'u_status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->u_auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'u_created_at',
                'updatedAtAttribute' => 'u_updated_at'
            ]
        ];
    }
    public static function tableName(){
        return "{{%user}}";
    }

    public function attributeLabels(){
        return [
            'u_id' => Yii::t('app','用户id'),
            'u_username' => Yii::t('app','用户名'),
            'u_auth_key' => Yii::t('app','用户验证密钥'),
            'u_password_hash' => Yii::t('app','密码hash'),
            'u_password_reset_token' => Yii::t('app','密码重设token'),
            'u_email' => Yii::t('app', '用户邮箱'),
            'u_status' => Yii::t('app', '用户状态'),
            'u_created_at' => Yii::t('app', '创建时间'),
            'u_updated_at' => Yii::t('app', '更新时间'),
            'u_created_at_format' => Yii::t('app', '创建时间'),
            'u_updated_at_format' => Yii::t('app', '更新时间'),
            'password' => Yii::t('app', '用户密码'),
            'password_confirm' => Yii::t('app', '用户确认密码'),
            'u_auth_status' => Yii::t('app', '验证状态'),
            'login_id' => Yii::t('app', '登录名称')
        ];
    }

    public static function getValidConsts($type, $onlyValue = false){
        if(empty(self::$_constMap)){
            self::$_constMap = [
                'u_status' => [
                    self::STATUS_ACTIVE => Yii::t('app','可用'),
                    self::STATUS_LOCKED => Yii::t('app', '锁定'),
                    self::STATUS_DELETE => Yii::t('app', '删除')
                ],
                'u_auth_status' => [
                    self::STATUS_NO_AUTH => Yii::t('app', '未验证'),
                    self::STATUS_AUTHED => Yii::t('app', '已经验证'),
                ],
            ];
        }
        if(array_key_exists($type, self::$_constMap) && !empty(self::$_constMap[$type])){
            return $onlyValue ? array_keys(self::$_constMap[$type]) : self::$_constMap[$type];
        }else{
            throw new \Exception("zh:不存在常量映射定义{$type}");
        }
    }
    public function rules(){
        return [
            ['u_username', 'required'],
            ['u_username', 'match', 'pattern' => '/[a-zA-Z0-9_\-]/'],
            ['u_username', 'string', 'min' => 5, 'max' => 30],
            ['u_username', 'unique', 'targetClass' => self::className()],

            ['u_email', 'required'],
            ['u_email', 'email'],
            ['u_email', 'unique', 'targetClass' => self::className()],

            ['u_status', 'required'],
            ['u_status', 'in', 'range' => self::getValidConsts('u_status', true)],

            ['u_auth_status', 'default', 'value' => User::STATUS_NO_AUTH],
            ['u_auth_status', 'in', 'range' => self::getValidConsts('u_auth_status', true)],

            ['password', 'required', 'on' => 'create'],
            ['password', 'required', 'on' => 'update', 'skipOnEmpty' => true],

            ['password', 'string', 'min' => 6, 'max' =>  20],

            ['password_confirm', 'required', 'on' => 'create'],
            ['password_confirm', 'required', 'on' => 'update', 'skipOnEmpty' => true],
            ['password_confirm', 'compare', 'compareAttribute' => 'password'],



        ];
    }
    public function validatePassword($attribute){
        if(!$this->hasErrors()){
            $userModel = new UserModel();
            if(!$userModel->validatePassword(['u_username' => $this->_login_id], $this->password)){
                list($code, $error) = $userModel->getOneError();
                $this->addError($code, $error);
            }
        }
    }
    public function scenarios(){
        return [
            'create' => [
                'u_username',
                'u_email',
                'u_status',
                'u_auth_status',
                'password',
                'password_confirm'
            ],
            'update' => [
                'u_status',
                'u_auth_status',
                'password',
                'password_confirm'
            ],
        ];
    }
    public function getIsActive(){
        return self::STATUS_ACTIVE == $this->u_status;
    }

    public function getIsLoacked(){
        return self::STATUS_LOCKED == $this->u_status;
    }

    public function getIsNoAuth(){
        return self::STATUS_NO_AUTH == $this->u_auth_status;
    }

    public function getIsAuthed(){
        return self::STATUS_AUTHED == $this->u_auth_status;
    }

    public function getU_created_at_format(){
        return date('Y-m-d H:i:s', $this->u_created_at);
    }

    public function getU_updated_at_format(){
        return date('Y-m-d H:i:s', $this->u_updated_at);
    }


}
