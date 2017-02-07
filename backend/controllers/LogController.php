<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController;
use common\models\log\LogModel;
use yii\helpers\VarDumper;

class LogController extends AdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionOne($id, $table_name){
        $logModel = new LogModel();
        $one = $logModel->getOne(['id' => $id], $table_name);
        echo VarDumper::dumpAsString($one);
        exit();
    }

    public function actionSearch(){
        $logModel = new LogModel();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $condition = $logModel->parseQueryCondtion($post);
            $sortData = $logModel->parseQuerySort($post);
            $tableName =  $post['table_name'];
            list($provider, $pagination) = $logModel->getProvider($condition, $sortData, $tableName, true);
        }else{
            $tableName = 'log_backend';
            $condition = [];
            $sortData = [];
            $provider = null;
        }
        return $this->render('search', [
            'provider' => $provider,
            'tableName' => $tableName
        ]);
    }

}
