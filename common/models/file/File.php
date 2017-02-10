<?php
namespace common\models\file;

use Yii;
use common\helpers\ExifTool;
use yii\db\ActiveRecord;

/**
 *
 */
class File extends ActiveRecord
{
    CONST DR_DISK = 'disk';
    CONST DR_OSS = 'oss';

    const FILE_ACL_PUB_R = 'pub-read';
    const FILE_ACL_PUB_RW= 'pub-read-write';
    const FILE_ACL_PRI = 'pri';

    const SP_LOCAL = 'local';

    const STATUS_UPLOADED = 's_uploaded';
    const STATUS_IN_QUEUE = 's_in_queue';

    static public $constMap = [];

    private $_host = null;
    private $_saveDir = null;



    /**
     * 源文件地址
     * @var [type]
     */
    public $source_path = null;

    /**
     * 源文件地址类型
     * @var [type]
     */
    public $source_path_type = null;

    /**
     * 是否异步保存
     * @var [type]
     */
    public $save_asyc = false;

    public static function tableName(){
        return "{{%file}}";
    }
    public static function getValidCategories($onlyValue = false){
        $data = require_once(Yii::getAlias('@common/models/file/categories.php'));
        return $onlyValue ? array_keys($data) : $data;
    }
    public static function getValidConsts($type, $onlyValue = false){
        if(empty(self::$constMap)){
            self::$constMap = [
                'f_storage_type' => [
                    self::DR_DISK => Yii::t('app','本地存储'),
                    self::DR_OSS => Yii::t('app', '阿里OSS')
                ],
                'f_acl_type' => [
                    self::FILE_ACL_PUB_R => Yii::t('app', '文件公共读'),
                    self::FILE_ACL_PUB_RW => Yii::t('app', '文件公共读写'),
                    self::FILE_ACL_PRI => Yii::t('app', '文件私有'),
                ],
                'f_category' => self::getValidCategories(),
                'source_path_type' => [
                    self::SP_LOCAL => '本地文件'
                ]
            ];
        }
        if(array_key_exists($type, self::$constMap) && !empty(self::$constMap[$type])){
            return $onlyValue ? array_keys(self::$constMap[$type]) : self::$constMap[$type];
        }else{
            throw new \Exception("zh:不存在常量映射定义{$type}");
        }
    }

    public function validateSourcePath($attr){
        if(self::SP_LOCAL == $this->source_path_type && !is_file($this->source_path)){
            $this->addError($attr, "zh:{$this->source_path} 文件不存在");
        }
    }

    public function setSaveDir($base){
        if($base && $this->f_category && $this->f_prefix){
            $dir =  $this->_saveDir = rtrim(implode(DIRECTORY_SEPARATOR, [
                $base,
                md5(
                    trim($this->f_category, DIRECTORY_SEPARATOR),
                    trim($this->f_prefix  , DIRECTORY_SEPARATOR)
                )
            ]), DIRECTORY_SEPARATOR);
        }elseif($base && $this->f_category){
            $dir =  $this->_saveDir = rtrim(implode(DIRECTORY_SEPARATOR, [
                $base,
                md5(
                    trim($this->f_category, DIRECTORY_SEPARATOR)
                )
            ]), DIRECTORY_SEPARATOR);
        }elseif($base){
            $dir = $base;
        }else{
            throw new \Exception(Yii::t('app', "base路径不能为空"));
        }
        $this->_saveDir = $dir;
    }

    public function getFilePath(){
        return implode( DIRECTORY_SEPARATOR,[
            trim(implode(DIRECTORY_SEPARATOR, [
                trim($this->f_category, DIRECTORY_SEPARATOR),
                trim($this->f_prefix, DIRECTORY_SEPARATOR),
            ]), DIRECTORY_SEPARATOR),
            $this->buildTotalName()
        ]);
    }

    public function getFileSavePath(){
        return implode(DIRECTORY_SEPARATOR, [$this->_saveDir, $this->buildTotalName()]);
    }

    public function getSaveDir(){
        return $this->_saveDir;
    }

    public function caculateSize(){
        return filesize($this->source_path);
    }
    public function parseMimeType(){
        $exiftool = new ExifTool($this->source_path);
        return $exiftool->getMimeType();
    }
    public function parseMetaData(){
        $exiftool = new ExifTool($this->source_path);
        return $exiftool->getMetaData();
    }

    public function buildTotalName(){
        if(!$this->f_ext){
            $exiftool = new ExifTool($this->source_path);
            $this->f_ext = $exiftool->getFileExt();
        }
        return $this->f_name . ($this->f_ext ? ('.' . $this->f_ext) : '');
    }

    public function getIsPublic(){
        return in_array($this->f_acl_type, [
            self::FILE_ACL_PUB_R,
            self::FILE_ACL_PUB_RW
        ]);
    }

    public function getIsPrivate(){
        return self::FILE_ACL_PRI == $this->f_acl_type;
    }

    public function getIsInQueue(){
        return self::STATUS_IN_QUEUE == $this->f_status;
    }

    public function getIsUploaded(){
        return self::STATUS_UPLOADED == $this->f_status;
    }


    public function rules(){
        return [
            ['f_storage_type', 'required'],
            ['f_storage_type', 'in', 'range' => self::getValidConsts('f_storage_type', true)],

            ['source_path_type', 'required'],
            ['source_path_type', 'in', 'range' => self::getValidConsts('source_path_type', true)],

            ['source_path', 'required'],
            ['source_path', 'validateSourcePath'],

            ['save_asyc', 'filter', 'filter' => 'boolval'],


            ['f_name', 'required'],
            ['f_name', 'unique', 'targetClass' => self::className()],
            ['f_name', 'match', 'pattern' => '/^[a-zA-Z0-9_][a-zA-Z0-9_\-]+$/'],

            ['f_acl_type', 'required'],
            ['f_acl_type', 'in', 'range' => self::getValidConsts('f_acl_type', true)],

            ['f_ext', 'match', 'pattern' => '/^[0-9a-zA-Z]+$/'],

            ['f_category', 'required'],
            ['f_category', 'in', 'range' => self::getValidConsts('f_category', true)],
            ['f_category', 'filter', 'filter' => function($value){
                return trim($value, '/');
            }],
        ];
    }



    public function scenarios(){
        return [
            'create' => [
                'source_path_type',
                'source_path',
                'save_asyc',
                'f_name',
                'f_category',
                'f_ext',
                'f_storage_type',
                'f_acl_type'
            ],
        ];
    }



}
