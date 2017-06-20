<?php
namespace common\models\log;

use Yii;
use common\base\Model;
use common\models\log\tables\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use common\models\user\tables\User;

class ActionModel extends Model{

    static protected $tpls = [];
    CONST M_USER = 1; // 用户模块
    CONST M_FILE = 2; // 文件模块

    protected function getArAttributes(){
        return (new Action())->attributes();
    }

    public static function getSummaryTpl($module, $actionName){
        if(empty(self::$tpls)){
            self::$tpls = require_once(Yii::getAlias('@common/config/action-def.php'));
        }
        if(isset(self::$tpls[$module]) && isset(self::$tpls[$module][$actionName])){
            return self::$tpls[$module][$actionName]['tpl'];
        }
        return '';
    }
    /**
     * 记录一个动作
     * @param  [type] $module   模块，或者业务类别，又本model分配, 必须
     * @param  [type] $uid      用户中心uid， 必须
     * @param  [type] $action   用户动作，字符串，20个字以内，允许字符[a-z0-9\-]， 必须
     * @param  [type] $objectId 对象id， 必须
     * @param  [type] $others  其他记录信息， 可选
     * [
     * 		'ip' => // 记录ip地址，可选，空自动获取
     * 		'agent_info' => // 代理信息， 空自动获取
     * 		'app_id' => // 空自动获取， 默认为1
     * 		'data' => // 相关数据
     * ]
     * @return [type]           [description]
     */
    public static function log($module, $uid, $actionName, $objectId, $others = []){
        if(!preg_match("/^[a-z0-9\_]{1,20}$/", $actionName)){
            throw new \Exception("用户动作，字符串，20个字以内，允许字符[a-z0-9\-]， 必须");
        }
        if(empty($module) || !is_int($module)){
            throw new \Exception("module必须存在且为数字");
        }
        if(empty($uid) || !is_int($uid)){
            throw new \Exception("uid必须存在且为数字");
        }
        if(!is_int($objectId)){
            throw new \Exception("objectId必须存在且为数字");
        }
        $action = new Action();
        $action->al_module = $module;
        $action->al_uid = $uid;
        $action->al_action = $actionName;
        $action->al_object_id = $objectId;
        $action->al_created_time = time();
        $action->al_ip = empty($others['ip']) ?
                        (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '') :
                        $others['ip'];
        $action->al_agent_info = empty($others['agent_info']) ?
                        (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') :
                        $others['agent_info'];
        $action->al_app_id = empty($others['app_id']) ? 1 : $others['app_id'];
        $action->al_data = empty($others['data']) ? '' : $otehrs['data'];
        return $action->insert(false);
    }

    /**
     * 获取动作描述列表
     * @param  [type]  $conditon @see fetchActions
     * @param  [type]  $vars     填充模板的额外变量
     * @param  integer $sort     排序方式，默认降序
     * @param  [type]  $withPage 是否分页，1
     * @return [type]            [description]
     */
    public function fetchActionsSummary($conditon, $vars = [], $sort=1, $withPage = true){
        list($provider, $pagination) = self::fetchActions($conditon, $sort, $withPage);
        $data = $provider->getModels();
        if(empty($data)){
            return $data;
        }
        // todo 加入用户名字

        $summarys = [];
        foreach($data as $item){
            $tpl = self::getSummaryTpl($item['al_module'], $item['al_action']);
            if($tpl){
                $item['time'] = date('Y-m-d H:i:s', $item['al_created_time']);
                foreach($item as $key => $value){
                    $item["%{$key}%"] = $value;
                    unset($item[$key]);
                }
                $item = array_merge($vars, $item);
                $summarys[] = [
                    'summary' => strtr($tpl, $item)
                ];
            }else{
                $summarys[] = [
                    'summary' => sprintf('模块%s动作%s没有模板，无法生成描述。', $item['al_module'], $item['al_action'])
                ];
            }
        }
        return $summarys;
    }

    public function joinExtra(Array $data = []){
        // fetch usernames
        $uids = array_keys(ArrayHelper::index($data, 'al_uid'));
        $users = User::find()->where(['u_id' => $uids])
                           ->select(['u_username', 'u_id'])
                           ->asArray()
                           ->indexBy('u_id')
                           ->all();

        foreach ($data as $key => $action) {
            // join username
            $data[$key]['al_uname'] = $action['al_uname'] = $users[$action['al_uid']]['u_username'];

            // join summary
            $tpl = self::getSummaryTpl($action['al_module'], $action['al_action']);
            $actionMap = $action;
            $actionMap['al_timestr'] = date('Y-m-d H:i:s', $actionMap['al_created_time']);
            foreach($actionMap as $name => $value){
                $actionMap["%{$name}%"] = $value;
                unset($actionMap[$name]);
            }
            $data[$key]['al_summary'] = strtr($tpl, $actionMap);
        }
        return $data;
    }


    /**
     * 查询条件
     * @param  [type] $conditon [description]
     * [
     * 		'al_module' => // 类型, 必须
     * 		'al_uid' => // 用户中心id， 可选
     * 		'al_action' => // 动作， 可选
     * 		'al_object_id' => // 数据， 可选
     * ]
     * // 获取关于某对象1, 关于动作a的所有记录
     * al_module => {$module}
     * al_action => {$action}
     * al_object_id => {$objectId}
     *
     * // 获取某用户1 关于某对象1, 关于动作a的所有记录
     * al_module => {$module}
     * al_action => {$action}
     * al_object_id => {$objectId},
     * al_uid => {$uid}
     *
     * @return [type]           [description]
     */
    public static function getProvider($conditon, $sort=1, $withPage = true){
        $query = Action::find()->where($conditon)->asArray();

        $defaultOrder = [
            'al_created_time' => $sort > 0 ? SORT_DESC : SORT_ASC
        ];
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
                'attributes' => ['al_created_time'],
                'defaultOrder' => $defaultOrder
            ]
        ]);
        $pagination = $provider->getPagination();
        return [$provider, $pagination];
    }





}
