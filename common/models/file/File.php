<?php
namespace common\models\file;

use Yii;
use common\helpers\ExifTool;
use common\base\ActiveRecord;
use common\models\file\FileModel;

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

    const SAVE_SYC = 0;
    CONST SAVE_ASYC= 1;

    const TMP_FILE = 1;
    const PERMANENT_FILE = 2;

    const SP_LOCAL = 'local';

    const STATUS_UPLOADED = 's_uploaded';
    const STATUS_IN_QUEUE = 's_in_queue';
    const STATU_UPLOAD_FAIL = 's_upload_failed';

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

    private $_upload_file = null;

    public static function tableName(){
        return "{{%file}}";
    }

    public static function logicModelName(){
        return FileModel::className();
    }

    public static function getValidCategories($onlyValue = false){
        $data = require_once(Yii::getAlias('@common/models/file/categories.php'));
        return $onlyValue ? array_keys($data) : $data;
    }
    public static function getValidConsts($type = null, $onlyValue = false){
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
                    self::SP_LOCAL => Yii::t('app', '本地文件')
                ],
                'f_status' => [
                    self::STATUS_UPLOADED => Yii::t('app', '已经上传'),
                    self::STATUS_IN_QUEUE => Yii::t('app', '等待上传（队列中）'),
                    self::STATU_UPLOAD_FAIL => Yii::t('app', '上传失败')
                ],
                'save_asyc' => [
                    self::SAVE_SYC => Yii::t('app', '同步保存'),
                    self::SAVE_ASYC => Yii::t('app', '异步保存'),
                ],
                'f_valid_type' => [
                    self::TMP_FILE => Yii::t('app', '临时文件'),
                    self::PERMANENT_FILE => Yii::t('app', '永久文件')
                ]
            ];
        }
        if(array_key_exists($type, self::$constMap) && !empty(self::$constMap[$type])){
            return $onlyValue ? array_keys(self::$constMap[$type]) : self::$constMap[$type];
        }elseif(null == $type){
            return self::$constMap;
        }else{
            throw new \Exception("zh:不存在常量映射定义{$type}");
        }
    }

    public function validateSourcePath($attr){
        if(self::SP_LOCAL == $this->source_path_type && !is_file($this->source_path)){
            $this->addError($attr, "zh:{$this->source_path} 文件不存在");
        }
    }
    public function setUpload_file($value){
        $this->_upload_file = $value;
    }
    public function getUpload_file(){
        return $this->_upload_file;
    }
    public function setSaveDir($base){
        $base = rtrim($base, DIRECTORY_SEPARATOR);
        if(!$base){
            throw new \Exception(Yii::t('app', 'base路径不能为空'));
        }
        $category = trim($this->f_category, DIRECTORY_SEPARATOR);
        $prefix = trim($this->f_prefix, DIRECTORY_SEPARATOR);
        $path = $category . ($prefix ? (DIRECTORY_SEPARATOR . $prefix) : '');
        $this->_saveDir = $base . DIRECTORY_SEPARATOR . md5($path);
    }

    public function getFilePath(){
        return implode( DIRECTORY_SEPARATOR,[
            trim(implode(DIRECTORY_SEPARATOR, [
                md5(trim($this->f_category, DIRECTORY_SEPARATOR)),
                trim($this->f_prefix, DIRECTORY_SEPARATOR),
            ]), DIRECTORY_SEPARATOR),
            $this->buildTotalName()
        ]);
    }

    public function getFileSavePath(){
        $driver = FileModel::instanceDriver($this->f_storage_type);
        $this->setSaveDir($driver->base);
        return implode(DIRECTORY_SEPARATOR, [
            rtrim($this->_saveDir, DIRECTORY_SEPARATOR),
            $this->buildTotalName()
        ]);
    }

    public function getSaveDir(){
        return $this->_saveDir;
    }

    public function getMetaObj(){
        $exif = new ExifTool();
        $exif->setMetaData(json_decode($this->f_meta_data, true));
        return $exif;
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

    public function getExt_img_url(){
        return $this->getLogicModel()->getExtImgUrl($this);
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

            ['save_asyc', 'filter', 'filter' => 'intval'],


            ['f_valid_type', 'default', 'value' => self::TMP_FILE],
            ['f_valid_type', 'filter', 'filter' => 'intval'],
            ['f_valid_type', 'in', 'range' => self::getValidConsts('f_valid_type', true)],


            ['f_name', 'required', 'skipOnEmpty' => true],
            ['f_name', 'unique', 'targetClass' => self::className()],
            ['f_name', 'match', 'pattern' => '/^[a-zA-Z0-9_][a-zA-Z0-9_\-.]+$/'],

            ['f_acl_type', 'required'],
            ['f_acl_type', 'in', 'range' => self::getValidConsts('f_acl_type', true)],


            ['f_ext', 'match', 'pattern' => '/^[0-9a-zA-Z]+$/'],

            ['f_category', 'required'],
            ['f_category', 'in', 'range' => self::getValidConsts('f_category', true)],
            ['f_category', 'filter', 'filter' => function($value){
                return trim($value, '/');
            }],
            ['f_prefix', 'filter', 'filter' => function($value){
                return trim($value, '/');
            }],

            ['upload_file', 'required', 'skipOnEmpty' => true]
        ];
    }

    public function attributeLabels(){
        return [
            'f_id' => Yii::t('app', '文件id'),
            'f_name' => Yii::t('app', '文件保存名称'),
            'f_depostion_name' => Yii::t('app', '文件下载名称'),
            'f_prefix' => Yii::t('app', '文件路径前缀'),
            'f_host' => Yii::t('app', '文件域名'),
            'f_category' => Yii::t('app', '文件分类'),
            'f_hash' => Yii::t('app', '文件hash值'),
            'f_ext' => Yii::t('app', '文件后缀'),
            'f_mime_type' => Yii::t('app', '文件mime-type'),
            'f_meta_data' => Yii::t('app', '文件元数据'),
            'f_size' => Yii::t('app', '文件大小'),
            'f_storage_type' => Yii::t('app', '文件存储类型'),
            'f_acl_type' => Yii::t('app', '文件访问类型'),
            'f_created_at' => Yii::t('app', '文件创建时间'),
            'f_updated_at' => Yii::t('app', '文件更新时间'),
            'f_status' => Yii::t('app', '文件状态'),
            'f_valid_type' => Yii::t('app', '文件时效类型'),
            'source_path' => Yii::t('app', '文件路径'),
            'source_path_type' => Yii::t('app', '文件路径类型'),
            'save_asyc' => Yii::t('app', '是否异步保存'),
            'upload_file' => Yii::t('app', '上传的文件'),
            'ext_img_url' => Yii::t('app', '文件类型所略图')
        ];
    }

    public function scenarios(){
        return [
            'create' => [
                'upload_file',
                'source_path_type',
                'source_path',
                'save_asyc',
                'f_name',
                'f_valid_type',
                'f_category',
                'f_prefix',
                'f_ext',
                'f_storage_type',
                'f_acl_type'
            ],
        ];
    }



}
