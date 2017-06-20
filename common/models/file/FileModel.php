<?php
namespace common\models\file;

use Yii;
use common\base\Model;
use common\models\file\File;
use common\models\file\DiskDriver;
use yii\web\HttpException;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\db\Query;


/**
 * 需要在queryId上标记存储位
 */
class FileModel extends Model
{
    static private $amqpConn;
    static private $Channel;

    protected function getValidPrefix(){
        // todo localhost
        return [
            'localhost',
            'trainor-oss-test.oss-cn-shenzhen.aliyuncs.com'
        ];
    }

    protected function buildIdFromUrl($url){
        $urlparts = parse_url($url);
        // todo localhost
        if(in_array($urlparts['host'], ['localhost'])){
            if(preg_match('/name=([^\&]*)/', $urlparts['query'], $matches)){
                return $matches[1];
            }else{
                return false;
            }
        }
        return false;
    }
    public static function parseQueryId($string){
        $r = preg_match('/^(disk|oss):{1}(.+)/', $string, $matches);
        if($r){
            return [$matches[1], $matches[2]];
        }else{
            return [null, null];
        }
    }
    protected function parseQIdFromUrls($urls){
        $prefix = $this->getValidPrefix();
        $result = [];
        foreach($urls as $url){
            $url = urldecode($url);
            $one = parse_url($url);
            if(in_array($one['host'], $prefix)){
                $qid = $this->buildIdFromUrl($url);
                if(false !== $qid){
                    $result[] = $qid;
                }
            }
        }
        return array_unique($result);;
    }

    protected function getUrlFromContent($content){
        if(preg_match_all('/(http:\/\/.*?)[\s\"\'\n]+/', $content, $matches)){
            return $matches[1];
        }else{
            return [];
        }
    }
    public function setFilePermanentFromArray($newQIds, $oldQIds = []){

        foreach($newQIds as $key => $nId){
            $newQIds[$key] = urldecode($nId);
        }
        foreach($oldQIds as $key => $oId){
            $oldQIds[$key] = urldecode($oId);
        }
        // 得到删除的id
        $deleteQIds = array_diff_assoc($oldQIds, $newQIds);
        $newPermanentQIds = array_diff_assoc($newQIds, $oldQIds);
        $report = [
            'p_succ' => [],
            'p_fail' => [],
            'd_succ' => [],
            'd_fail' => []
        ];
        if(!empty($newPermanentQIds)){
            // 设置文件为有效
            foreach ($newPermanentQIds as $queryId) {
                $condition = $this->buildConditionByQueryId($queryId);
                $file = $this->setFilePermanent($condition);
                if(!$file){
                    $report['p_fail'][] = $queryId;
                }else{
                    $report['p_succ'][] = $queryId;
                }
            }
        }
        if(!empty($deleteQIds)){
            // 设置文件为临时文件
            foreach ($deleteQIds as $queryId) {
                $condition = $this->buildConditionByQueryId($queryId);

                $file = $this->setFileTmp($condition);

                if(!$file){
                    $report['d_fail'][] = $queryId;
                }else{
                    $report['d_succ'][] = $queryId;
                }
            }
        }
        // todo 不应该怎么鲁莽的
        return true;
    }

    public function setFilePermanentFromContent($newContent, $oldContent = null){
        $newQIds = $this->parseQIdFromUrls($this->getUrlFromContent($newContent));
        $report = [
            'p_succ' => [],
            'p_fail' => [],
            'd_succ' => [],
            'd_fail' => []
        ];
        if(!empty($newQIds)){
            if(!empty($oldContent)){
                $oldQIds = $this->parseQIdFromUrls($this->getUrlFromContent($oldContent));
            }else{
                $oldQIds = [];
            }
            // 得到删除的id
            $deleteQIds = array_diff_assoc($oldQIds, $newQIds);
            $newPermanentQIds = array_diff_assoc($newQIds, $oldQIds);
            if(!empty($newPermanentQIds)){
                // 设置文件为有效
                foreach ($newPermanentQIds as $queryId) {
                    $condition = $this->buildConditionByQueryId($queryId);
                    $file = $this->setFilePermanent($condition);
                    if(!$file){
                        $report['p_fail'][] = $queryId;
                    }else{
                        $report['p_succ'][] = $queryId;
                    }
                }
            }
            if(!empty($deleteQIds)){
                // 设置文件为临时文件
                foreach ($deleteQIds as $queryId) {
                    $condition = $this->buildConditionByQueryId($queryId);
                    $file = $this->setFileTmp($condition);
                    if(!$file){
                        $report['d_fail'][] = $queryId;
                    }else{
                        $report['d_succ'][] = $queryId;
                    }
                }
            }
        }
        // todo 不应该怎么鲁莽的
        return true;
    }

    public function deleteOneFile($condition){
        $file = $this->getOne($condition);
        if(!$file){
            return true;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 1. 删除数据库记录
            $result = $file->delete();
            if(false === $result){
                $this->addError('', '删除失败');
                return false;
            }

            // 2. 删除文件
            $result = $this->deleteFileFromTps($file->f_storage_type, $file->getQueryId());

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            Yii::error($e);
            $transaction->rollback();
            $this->addError('', '发生异常');
            return false;
        }
    }

    protected function deleteFileFromTps($storageType, $queryId){
        $driver = $this->instanceDriver($storageType);
        return $driver->deleteFile($queryId);
    }
    public function savePrimaryIds($ids = []){
        foreach($ids as $key => $id){
            $ids[$key] = ['vfi_fid' => $id];
        }
        $command = Yii::$app->db->createCommand();
        $command->batchInsert("{{%valid_file_id}}", ['vfi_fid'], $ids);
        $command->execute();
        return true;
    }
    public function getTmpFileIds(){
        $query = $this->getTmpFileQuery();
        $result = $query->select(['f_id'])->asArray()->all();
        if(!empty($result)){
            $result = ArrayHelper::getColumn($result, 'f_id');
        }
        return $result;
    }


    protected function getTmpFileQuery(){
        $query = File::find();
        $query->andWhere(['=', 'f_valid_type', File::TMP_FILE]);
        return $query;
    }


    public function getProvider($condition = [], $sortData = [], $withPage = true){
        $query = File::find();
        $query = $this->buildQueryWithCondition($query, $condition);

        $defaultOrder = [
            'f_created_at' => SORT_DESC
        ];

        if(!empty($sortData)){
            $defaultOrder = $sortData;
        }
        $pageConfig = [];
        if(!$withPage){
            $pageConfig['pageSize'] = 0;
        }else{
            $pageConfig['pageSize'] = 20;
        }
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pageConfig,
            'sort' => [
                'attributes' => ['f_created_at'],
                'defaultOrder' => $defaultOrder
            ]
        ]);
        $pagination = $provider->getPagination();
        return [$provider, $pagination];
    }


    protected function buildConditionByQueryId($queryId){
        list($type, $path) = self::parseQueryId($queryId);
        $pathParts = pathinfo($path);
        $condition = [];
        if(isset($pathParts['dirname'])){
            $condition['f_category'] = $pathParts['dirname'];
        }
        if(isset($pathParts['filename'])){
            $condition['f_name'] = $pathParts['filename'];
        }
        if(isset($pathParts['extension'])){
            $condition['f_ext'] = $pathParts['extension'];
        }
        $condition['f_storage_type'] = $type;
        return $condition;
    }
    public function getOneByQueryId($queryId){
        $condition = $this->buildConditionByQueryId($queryId);
        return !empty($condition) ? $this->getOne($condition) : null;
    }

    public function getOne($condition){
        if(is_object($condition)){
            return $condition;
        }
        if(!empty($condition)){
            return File::find()->where($condition)->one();
        }else{
            return null;
        }
    }

    public function getFileUrl($id){
        // todo 这个方法应该改造到可以不需要查询数据库
        $queryId = null;
        $host = null;
        $isPublic = true;
        $type = null;
        if(($id instanceof File)){
            $queryId = $id->getQueryId();
            $host = $id->f_host;
            $isPublic = $id->isPublic;
            $type = $id->f_storage_type;
        }elseif(is_integer($id)){
            $file = $this->getOne(['f_id' => $id]);
            if(!$file){
                return null;
            }
            $queryId = $file->getQueryId();
            $host = $file->f_host;
            $isPublic = $file->isPublic;
            $type = $file->f_storage_type;
        }else{
            $queryId = $id;
            list($type, $path) = $this->parseQueryId($id);
            if($type){
                return null;
            }
        }
        $driver = $this->instanceDriver($type);
        $url = $driver->getFileUrl($queryId, $host, $isPublic);
        return $url;
    }

    public function getLocalFileUrl($name){
        $driver = $this->instanceDriver(File::DR_DISK);
        return $driver->getFileUrl($name);
    }

    public function getLocalFilePath($name){
        $driver = $this->instanceDriver(File::DR_DISK);
        return $driver->getFilePath($name);
    }

    public function output($id){
        if(!($id instanceof File)){
            $file = $this->getOne(['f_id' => $id]);
        }else{
            $file = $id;
        }
        if(!$file){
            throw new HttpException(404, Yii::t('app', '数据不存在'));
        }
        $driver = $this->instanceDriver($file->f_storage_type);
        $driver->output($file);
    }

    public function setFileUploaded($file){
        $file = $this->getOne($file);
        if(!$file){
            $this->addError('', '数据不存在');
            return false;
        }
        $file->f_status = File::STATUS_UPLOADED;
        if(false === $file->update(false)){
            $this->addError('', Yii::t('app', '数据库更新失败'));
            return false;
        }
        return $file;
    }

    public function setFileFailed($file){
        $file = $this->getOne($file);
        if(!$file){
            $this->addError('', '数据不存在');
            return false;
        }
        $file->f_status = File::STATU_UPLOAD_FAIL;
        return $this->updateOneInner($file);
    }

    public function setFilePermanent($file){
        $file = $this->getOne($file);
        if(!$file){
            $this->addError('', '数据不存在');
            return false;
        }
        $file->f_valid_type = File::PERMANENT_FILE;
        return $this->updateOneInner($file);
    }

    public function setFileTmp($condition){
        $file = $this->getOne($condition);
        if(!$file){
            $this->addError('', '数据不存在');
            return false;
        }
        $file->f_valid_type = File::TMP_FILE;
        return $this->updateOneInner($file);
    }

    protected function updateOneInner($file){
        if(false === $file->update(false)){
            $this->addError('', Yii::t('app', '数据库更新失败'));
            return false;
        }
        return $file;
    }

    public static function deleteFiles($condition = []){
        File::deleteAll($condition);
    }

    /**
     * 保存文件
     * @param  [type] $data 提交过来的数据
     * upload_file 可选
     * source_path_type 原地址的类型，如 @see File::getValidConsts
     * source_path 原地址
     * save_asyc 保存的方式，同步保存还是异步保存
     * f_name 下载的文件名
     * f_valid_type 临时性定义，长期有效，临时文件
     * f_category 分类
     * f_prefix 前缀
     * f_ext 后缀名
     * f_storage_type 存储类型，本地还是oss
     * f_acl_type 私有，公有
     * @param  [type] $file [description]
     * @return [type]       [description]
     */
    public function saveFile($data, $file = null){
        if(!$file){
            $file = new File();
        }
        $file->scenario = 'create';
        if(!$file->load($data, '') || !$file->validate()){
            $this->addError('', $this->getArErrMsg($file));
            return false;
        }
        $this->buildSaveInfo($file);
        if(!$file->save_asyc){
            $file = $this->uploadFileToTps($file);
            if(!$file){
                return false;
            }
            $file->f_status = File::STATUS_UPLOADED;
        }else{
            // todo异步保存需要保存本地文件
            $file->f_status = File::STATUS_IN_QUEUE;
        }
        // insert record in db
        $file = $this->saveFileInfoInDb($file);
        if(!$file){
            return false;
        }
        // 加入到队列当中
        if($file->save_asyc){
            $this->sendFileDataAsyc($file);
        }

        return $file;
    }


    public function buildSaveInfo(File $file){
        $driver = FileModel::instanceDriver($file->f_storage_type);
        $driver->buildSaveInfo($file);
    }

    public function saveTmpFile($filePath){
        $file = new File();
        $file->f_storage_type = File::DR_DISK;
        $file->source_path = $filePath;
        $file->source_path_type = File::SP_LOCAL;
        $file->f_category = 'tmp_file';
        $path_parts = pathinfo($filePath);
        $file->f_name = $path_parts['filename'];
        return $this->uploadFileToTps($file);
    }

    public static function generateUniqueName(){
        return uniqid(time(), true);
    }

    public static function coverQidToPath($queryId){
        list($type, $qid) = self::parseQueryId($queryId);
        return md5(dirname($qid)) . DIRECTORY_SEPARATOR . basename($qid);
    }

    public static function hashPath($string){
        return md5($string);
    }


    public function moveTmpFileToTps($tmpName, $asyc = false){
        $path = $this->getLocalFilePath($tmpName);
        if(!file_exists($path)){
            $this->addError('', Yii::t('app', '文件不存在'.$path));
            return false;
        }
        $file = new File();
        $file->source_path = $path;
        $file->source_path_type = File::SP_LOCAL;
        $file->f_storage_type = File::DR_DISK;
        $file->f_acl_type = File::FILE_ACL_PRI;
        $file->f_category = 'post_thumb';
        $file->save_asyc = $asyc;
        $path_parts = pathinfo($tmpName);
        $file->f_name = $path_parts['filename'];

        $result = $this->saveFile(['f_name' => $path_parts['filename']], $file);
        if(!$result){
            return false;
        }
        return $file;
    }


    protected function uploadFileToTps($file){
        $driver = $this->instanceDriver($file->f_storage_type);
        $file = $driver->save($file);
        if(!$file){
            $this->addErrors($driver->getErrors());
            return false;
        }
        return $file;
    }

    protected function sendFileDataAsyc($file){
        $connection = $this->getAmqpConn();
        $channel = $this->getChannel();
        $msg = new AMQPMessage(json_encode([
            'f_id' => $file->f_id,
            'source_path' => $file->source_path,
            'source_path_type' => $file->source_path_type
        ]), ['delivery_mode' => 2]);
        $channel->basic_publish($msg, '', 'file-job');
    }

    protected function saveFileInfoInDb($file){
        $trans = Yii::$app->db->beginTransaction();
        try {
            if($id = $this->applyNewId()){
                $file->f_id = $id;
            }
            $file->f_size = $file->caculateSize();
            $file->f_mime_type = $file->parseMimeType();
            $file->f_meta_data = json_encode($file->parseMetaData());
            $file->f_created_at = time();
            $file->f_updated_at = time();
            $file->f_depostion_name = $file->getTotalName();
            if($file->insert(false)){
                $trans->commit();
                return $file;
            }else{
                return false;
            }
        } catch (\Exception $e) {
            Yii::error($e);
            $trans->rollback();
            $this->addError('', "插入发生异常");
            return false;
        }
    }

    protected function applyNewId(){
        $one = (new Query())
                 ->from("{{%valid_file_id}}")
                 ->one();
        if(empty($one)){
            return null;
        }else{
            $result = Yii::$app->db->createCommand("delete from {{%valid_file_id}} where vfi_id = :id", [
                ':id' => $one['vfi_id']
                ])->execute();
            return $one['vfi_fid'];
        }
    }

    public static function instanceDriver($type){
        switch ($type) {
            case File::DR_DISK:
                return Yii::$app->diskfile;
            case File::DR_OSS:
                return Yii::$app->ossfile;
            default:
                throw new \Exception("zh:不支持存储类型{$type}");
                break;
        }
    }
    public static function getExtImgMap(){
        return [
            'default' => 'txt_30x36.png',
            'pdf' => 'pdf_30x36.png',
            'zip' => 'zip_30x36.png',
            'doc' => 'doc_30x36.png',
            'etc' => 'etc_30x36.png',
            'jpg' => 'jpg_30x36.png',
            'ppt' => 'ppt_30x36.png',
            'wav' => 'wav_30x36.png',
            'video' => 'avi_30x36.png',
            'audio' => 'avi_30x36.png'
        ];
    }
    public function getExtImgUrl($file){
        $exifTool = $file->getMetaObj();
        $ext = $exifTool->getFileExt();
        if(!array_key_exists($ext, self::getExtImgMap())){
            if($exifTool->isVideo()){
                $ext = 'video';
            }elseif($exifTool->isAudio()){
                $ext = 'audio';
            }elseif($exifTool->isMSWord() || $exifTool->isWPSWord()){
                $ext = 'doc';
            }elseif($exifTool->isWPSPpt() || $exifTool->isMSPpt()){
                $ext = 'ppt';
            }else{
                $ext = 'default';
            }
        }
        return $this->getLocalFileUrl("file_thumbs/" . self::getExtImgMap()[$ext]);
    }

    private function getChannel(){
        if(self::$Channel){
            return self::$Channel;
        }
        $conn = $this->getAmqpConn();
        $channel = $conn->channel();
        $channel->queue_declare('file-job', false, true, false, false);
        return self::$Channel = $channel;
    }

    private function getAmqpConn(){
        if(self::$amqpConn){
            return self::$amqpConn;
        }
        return self::$amqpConn = new AMQPStreamConnection('localhost', 5672, 'kitral', 'philips');
    }
}
