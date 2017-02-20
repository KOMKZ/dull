<?php
namespace common\models\post\tables;

use Yii;
use common\base\ActiveRecord;
use common\models\user\tables\User;

/**
 *
 */
class Post extends ActiveRecord
{
    CONST TYPE_MK = 'markdown';
    CONST TYPE_HTML = 'html';
    CONST TYPE_RAW  = 'raw';

    CONST STATUS_ACTIVE = 'active';
    CONST STATUS_UNACTIVE = 'locked';


    static private $_constMap = [];

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'p_created_at',
                'updatedAtAttribute' => 'p_updated_at'
            ]
        ];
    }

    public static function tableName(){
        return "{{%post}}";
    }

    public function attributeLabels(){
        return [
            'p_id' => Yii::t('app', '文章id'),
            'p_title' => Yii::t('app', '文章标题'),
            'p_content' => Yii::t('app', '文章内容'),
            'p_thumb_img' => Yii::t('app', '文章封面图url'),
            'p_thumb_img_id' => Yii::t('app', '文章封面图id'),
            'p_created_uid' => Yii::t('app', '文章创建人id'),
            'p_status' => Yii::t('app', '文章状态'),
            'p_content_type' => Yii::t('app', '文章渲染类型'),
            'p_created_at' => Yii::t('app', '文章创建时间'),
            'p_updated_at' => Yii::t('app', '文章更新时间')
        ];
    }

    public static function getValidConsts($type, $onlyValue = false){
        if(empty(self::$_constMap)){
            self::$_constMap = [
                'p_content_type' => [
                    self::TYPE_RAW => Yii::t('app', '纯文本'),
                    self::TYPE_HTML => Yii::t('app', '富文本'),
                    self::TYPE_MK => Yii::t('app','markdown'),
                ],
                'p_status' => [
                    self::STATUS_ACTIVE => Yii::t('app', '可用'),
                    self::STATUS_UNACTIVE => Yii::t('app', '不可用')
                ]
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
            ['p_title', 'required'],
            ['p_title', 'string', 'min' => 5, 'max' => 30],

            ['p_content', 'required'],

            // ['p_created_uid', 'required'],
            // ['p_created_uid', 'exist', 'targetClass' => User::className()],

            ['p_content_type', 'required'],
            ['p_content_type', 'in', 'range' => self::getValidConsts('p_content_type', true)],

            ['p_status', 'required'],
            ['p_status', 'in', 'range' => self::getValidConsts('p_status', true)]

        ];
    }
    public function scenarios(){
        return [
            'create' => [
                'p_title',
                'p_content',
                'p_thumb_img_url',
                'p_status',
                // 'p_created_uid',
                'p_content_type'
            ]
        ];
    }


}
