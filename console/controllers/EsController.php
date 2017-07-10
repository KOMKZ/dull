<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class EsController extends Controller{

    CONST CRON_NAME = 'cron_es2search';

    public function init(){
        parent::init();
        Yii::$app->log->targets['es2search']->enabled = true;
    }
    /**
     * 初始化安装索引定义，映射定义，还有建索引，会删除原来的数据（不要随便使用）
     */
    public function actionInstall(){
        $this->actionInit();
        $this->actionInstallIndex();
        $this->actionInstallMapping();
        $this->actionUpdateData();
        file_put_contents(Yii::$app->log->targets['es2search']->logFile, "\n\n", FILE_APPEND);
    }
    /**
     * 更新索引数据
     * @return [type] [description]
     */
    public function actionUpdate(){
        $this->actionUpdateData();
        file_put_contents(Yii::$app->log->targets['es2search']->logFile, "\n\n", FILE_APPEND);
    }
    /**
     * 清空计时器（不要随便使用）
     * @return [type] [description]
     */
    public function actionInit(){
        $this->setTime('discuss', 0);
    }
    /**
     * 清空计时器文件（不要随便使用）
     * @return [type] [description]
     */
    public function actionClearTimeFile(){
        file_exists($this->getTimeFile()) ? unlink($this->getTimeFile()) : '';
    }
    /**
     * 安装索引定义（不要随便使用）
     * @return [type] [description]
     */
    public function actionInstallIndex(){
        $indexDef = $this->getIndexDef(Search2Model::getHse2Index());
        $es = Yii::$app->es;
        $exists = $es->indices()->exists(['index' => Search2Model::getHse2Index()]);
        if($exists){
            $es->indices()->delete(['index' => Search2Model::getHse2Index()]);
        }
        $r = $es->indices()->create($indexDef);
    }
    /**
     * 安装映射定义（不要随便使用）
     * @return [type] [description]
     */
    public function actionInstallMapping(){
        $discussDef = $this->getMappingDef('discuss');
        $es = Yii::$app->es;
        $params = [
            'index' => Search2Model::getHse2Index(),
            'type' => 'discuss',
            'body' => [
                'discuss' => $discussDef
            ]
        ];
        $es->indices()->putMapping($params);

    }

    /**
     * 更新讨论数据
     * @return [type] [description]
     */
    public function actionUpdateData(){
        echo "更新讨论索引 开始\n";
        $data = [];
        $this->begin('更新讨论索引 开始', [], self::CRON_NAME);
        try {
            $pdo = Yii::$app->db->getMasterPdo();
            $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            $es = Yii::$app->es;
            $uresult = $pdo->query(strtr($this->getDataSql('discuss'), ['%s' => $this->getTime('discuss')]));
            $rowTotal = 0;
            $indexTotal = 0;
            if ($uresult) {
               $params = ['body' => []];
               $perNum = 100;
               $n = 0;
               while ($row = $uresult->fetch(\PDO::FETCH_ASSOC)) {
                   $rowTotal++;
                   $params['body'][] = ['index' => ['_index' => Search2Model::getHse2Index(), '_type' => 'discuss', '_id' => $row['_id']]];
                   $params['body'][] = [
                        'uid' => $row['uid'],
                        'is_delete' => $row['is_delete'],
                        'view_count' => $row['view_count'],
                        'comment_count' => $row['comment_count'],
                        'popularity_count' => $row['popularity_count'],
                        'created_time' => $row['created_time'],
                        'uname' => $row['uname'],
                        'unickname' => $row['unickname'],
                        'title' => $row['title'],
                        'content' => $row['content'],
                        'reply_content' => $row['reply_content'],
                        'avatar_file' => $row['avatar_file']
                   ];
                   $n++;
                   if($n == $perNum){
                       $r = $es->bulk($params);
                       $succCount = count(array_filter(ArrayHelper::getColumn($r['items'], 'index._shards.successful'), function($val){return 1 == $val;}));
                       $indexTotal += $succCount;
                       $params = ['body' => []];
                       $n = 0;
                       echo sprintf("affects:%s\n", count($r['items']));
                   }
               }
               if($n != 0){
                   $r = $es->bulk($params);
                   $succCount = count(array_filter(ArrayHelper::getColumn($r['items'], 'index._shards.successful'), function($val){return 1 == $val;}));
                   $indexTotal += $succCount;
                   $params = ['body' => []];
                   $n = 0;
                   echo sprintf("affects:%s\n", count($r['items']));
               }
               $this->setTime('discuss', time());
               $data['indexTotal'] = $indexTotal;
               $data['rowTotal'] = $rowTotal;
            }
        } catch (\Exception $e) {
            Yii::error($e, self::CRON_NAME);
            $data['report'] = '发生异常';
        }
        $this->end('更新讨论索引 结束', $data, self::CRON_NAME);
    }


    protected function getDataSql($name){
        $sqls = require(Yii::getAlias('@common/config/es/esdata-sql.php'));
        if(array_key_exists($name, $sqls)){
            return $sqls[$name];
        }
        throw new \Exception("data {$name} does't exists.");
    }
    protected function getMappingDef($name){
        $defs = require(Yii::getAlias('@common/config/es/esindex-def.php'));
        if(array_key_exists($name, $defs['mappings'])){
            return $defs['mappings'][$name];
        }
        throw new \Exception("mapping {$name} does't exists.");
    }
    protected function getIndexDef($name){
        $defs = require(Yii::getAlias('@common/config/es/esindex-def.php'));
        if(array_key_exists($name, $defs['indices'])){
            return $defs['indices'][$name];
        }
        throw new \Exception("index {$name} does't exists.");
    }
    protected function getPks($sql, $name, $perNum = 500){
        $pdo = Yii::$app->db->getMasterPdo();
        $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $uresult = $pdo->query($sql);
        $pks = [];
        if ($uresult) {
           $idstr = '(';
           $n = 0;
           while ($row = $uresult->fetch(\PDO::FETCH_ASSOC)) {
               $idstr .= $row[$name] . ',';
               $n++;
               if($n == $perNum){
                   $idstr = rtrim($idstr, ',') . ')';
                   $pks[] = $idstr;
                   $n = 0;
                   $idstr = '(';
               }
           }
           if(0 != $n){
               $idstr = rtrim($idstr, ',') . ')';
               $pks[] = $idstr;
               $n = 0;
               $idstr = '(';
           }
        }
        return $pks;
    }
    protected function setTime($name, $value){
        $path = $this->getTimeFile();
        if(!is_dir($dir = dirname($path))){
            FileHelper::createDirectory($dir);
        }
        if(!file_exists($path)){
            touch($path);
        }
        if (($fp = @fopen($path, 'r+')) === false) {
            throw new \Exception("Unable to open debug data index file: $indexFile");
        }
        @flock($fp, LOCK_EX);
        $content = '';
        while (($buffer = fgets($fp)) !== false) {
            $content .= $buffer;
        }
        if (!feof($fp) || empty($content)) {
            // error while reading index data, ignore and create new
            $time = $this->getTimeStruct();
        } else {
            $time = unserialize($content);
        }
        $time[$name] = $value;
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, serialize($time));
        @flock($fp, LOCK_UN);
        @fclose($fp);
    }
    protected function getTime($name = null){
        $path = $this->getTimeFile();
        if(!file_exists($path)){
            throw new \Exception("{$path} doesn't exists.\n");
        }
        $time = unserialize(file_get_contents($path));
        return null === $name ? $time : $time[$name];
    }
    protected function getTimeStruct(){
        return [];
    }
    protected function getTimeFile(){
        $path = Yii::getAlias('@app/runtime/es/time.txt');
        return $path;
    }


    private $beginTime = null;

    private function begin($title, $data = [], $type = null){
        $this->beginTime = microtime(true);
        $data['title'] = $title;
        $data['flag'] = 'start';
        $data['time'] = time();
        Yii::info($data, $type);
    }
    private function end($title, $data, $type){
        $data['flag'] = 'end';
        $data['title'] = $title;
        $data['consume'] = $this->getConsume();
        $data['consume_report'] = sprintf('耗时：%s', $data['consume']);
        $data['time'] = time();
        Yii::info($data, $type);
    }
    private function getConsume(){
        return sprintf('%.3fs', microtime(true) - $this->beginTime);
    }

}
