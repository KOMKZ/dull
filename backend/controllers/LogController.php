<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController;
use common\models\log\LogModel;
use common\models\log\tables\LogBackend;
use yii\helpers\VarDumper;

class LogController extends AdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionOne($id, $table_name = 'log_backend'){
        $logModel = new LogModel();
        $one = $logModel->getOne(['id' => $id], $table_name);
        echo VarDumper::dumpAsString($one);
        exit();
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
            list($provider, $pagination) = $logModel->getProvider($condition, [], $table_name, true);
        }
        return $this->render('search', [
            'provider' => $provider
        ]);
    }

}
