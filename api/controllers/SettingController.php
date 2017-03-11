<?php
namespace api\controllers;

use Yii;
use common\models\setting\SettingModel;
use common\base\ApiController;
use yii\helpers\Html;



/**
 *
 */
class SettingController extends ApiController{
    public function actionUpdateAll(){
        $settingModel = new SettingModel();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $result = $settingModel->updateAllSettings($post);
            if(!$result){
                list($code, $error) = $settingModel->getOneError();
                return $this->error($code, $error);
            }
        }
        return $this->succ(null);
    }
}
