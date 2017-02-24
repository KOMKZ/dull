<?php
namespace backend\controllers;

use Yii;
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
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $notifyModel = new NotifyModel();
            $result = $notifyModel->createMsg($post['SysMsg']);
            if(!$result){
                list($code, $error) = $notifyModel->getOneError();
                $this->error($code, $error);
                return $this->refresh();
            }else{
                $this->succ();
                return $this->refresh();
            }
        }
        $sysMsg = new SysMsg();
        return $this->render('send', [
            'model' => $sysMsg,
            'mTplTypeMap' => NotifyModel::getMTplTypeMap(true),
            'mTplTypeData' => NotifyModel::getMTplTypeMap(),
            'mUseTplMap' => SysMsg::getValidConsts('sm_use_tpl'),
            'mSmObjectTypeMap' => SysMsg::getValidConsts('sm_object_type')
        ]);
    }



}
