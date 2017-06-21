<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController;
use common\models\log\LogModel;
use common\models\log\tables\LogBackend;
use yii\helpers\VarDumper;
use yii\i18n\Formatter;
use yii\helpers\Url;
use common\models\log\ActionModel;

class LogController extends AdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionOne($id, $table_name = ''){
        $logModel = new LogModel();
        $one = $logModel->getOne(['id' => $id], $table_name ? $table_name : 'log_backend');
        echo VarDumper::dumpAsString($one, 100, true);
        exit();
    }

    public function actionActionSearch(){
        $actionModel = new ActionModel();
        $get = Yii::$app->request->get();
        if(!empty($get['al_created_time'])){
            if(!empty($get['al_created_time']['start'])){
                $get['al_created_time']['start'] = strtotime($get['al_created_time']['start']);
            }
            if(!empty($get['al_created_time']['end'])){
                $get['al_created_time']['end'] = strtotime($get['al_created_time']['end']);
            }
        }
        if(!empty($get['al_action'])){
            list($module, $actionName) = explode('/', $get['al_action']);
            $get['al_action'] = $actionName;
        }
        $condition = $actionModel->parseQueryCondtion($get);
        list($provider, $pagination) = $actionModel->getProvider($condition, [], true);
        $provider->setModels($actionModel->joinExtra($provider->getModels()));
        $labels = $actionModel->getActionLabels();
        array_unshift($labels, '没有选择');
        return $this->render('action-search', [
            'provider' => $provider,
            'logSearchUrl' => Url::to(['log/action-search']),
            'labels' => $labels,
        ]);
    }

    public function actionSearch($table_name = 'log_backend'){
        $logModel = new LogModel();
        $get = Yii::$app->request->get();
        // table check
        if(!$logModel->isLogTableExists($table_name)){
            $provider = null;
        }else{
            // format
            if(!empty($get['log_time'])){
                if(!empty($get['log_time']['start'])){
                    $get['log_time']['start'] = strtotime($get['log_time']['start']);
                }
                if(!empty($get['log_time']['end'])){
                    $get['log_time']['end'] = strtotime($get['log_time']['end']);
                }
            }
            $condition = $logModel->parseQueryCondtion($get);
            list($provider, $pagination) = $logModel->getProvider($condition, [], true, $table_name);
        }
        return $this->render('search', [
            'provider' => $provider,
            'logSearchUrl' => Url::to(['log/search'])
        ]);
    }

}
