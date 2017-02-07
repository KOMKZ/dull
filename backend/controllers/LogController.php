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

    public function actionOne($id, $table_name = 'log_backend'){
        $logModel = new LogModel();
        $one = $logModel->getOne(['id' => $id], $table_name);
        echo VarDumper::dumpAsString($one);
        exit();
    }

    public function actionSearch($table_name = 'log_backend'){
        $logModel = new LogModel();
        list($provider, $pagination) = $logModel->getProvider([], [], $table_name, true);
        return $this->render('search', [
            'provider' => $provider
        ]);
    }

}
