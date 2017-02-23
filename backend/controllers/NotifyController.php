<?php
namespace backend\controllers;

use common\base\AdminController;
use common\models\notify\tables\SysMsg;
use common\models\notify\NotifyModel;

class NotifyController extends AdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSend(){
        $sysMsg = new SysMsg();
        return $this->render('send', [
            'model' => $sysMsg,
            'mTplTypeMap' => NotifyModel::getMTplTypeMap(true),
            'mTplTypeData' => NotifyModel::getMTplTypeMap(),
        ]);
    }



}
