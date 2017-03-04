<?php
namespace backend\controllers;

use common\base\AdminController;
use common\models\setting\tables\Setting;
use common\models\setting\SettingModel;

class SettingController extends AdminController
{

    public function actionIndex()
    {
        $settingModel = new SettingModel();
        $setting = $settingModel->all();
        return $this->render('index', [
            'settings' => $setting
        ]);
    }
    public function actionHome(){
        $settingModel = new SettingModel();
        $result = $settingModel->all();
    }
    public function actionDemo(){
        $data = [
            'set_name' => 'web_title',
            'set_value' => 'DULL',
            'set_value_type' => Setting::STRING,
            'set_des' => '网站名称',
            'set_module' => Setting::M_WEBSITE,
            'set_validators' => [
                ['string', 'min' => 1, 'max' => 50]
            ],
            'set_validators_params' => '',
            'set_widget' => Setting::W_TEXT,
            'set_widget_params' => ''
        ];
        $settingModel = new SettingModel();
        $result = $settingModel->create($data);
        if(!$result){
            console($settingModel->getOneError());
        }
    }

}
