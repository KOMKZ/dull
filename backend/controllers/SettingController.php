<?php
namespace backend\controllers;

use common\base\AdminController;
use common\models\setting\SettingModel;

class SettingController extends AdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionDemo(){
        $data = [
            'set_name' => 'web_title',
            'set_value' => 'DULL',
            'set_value_type' => 'string',
            'set_des' => '网站名称',
            'set_module' => 1,
            'set_parent_id' => 0,
            'set_validators' => [
                ['string', 'min' => 3, 'max' => 10]
            ],
            'set_validators_params' => '',
            'set_widget' => 'pure-text',
            'set_widget_params' => '',
            'set_active' => time()
        ];
        $settingModel = new SettingModel();
        $result = $settingModel->create($data);
        if(!$result){
            console($settingModel->getOneError());
        }
    }

}
