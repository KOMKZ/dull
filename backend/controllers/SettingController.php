<?php
namespace backend\controllers;

use Yii;
use common\base\AdminController;
use common\models\setting\tables\Setting;
use common\models\setting\SettingModel;
use common\models\setting\SettingWidget;

class SettingController extends AdminController
{

    public function actionIndex()
    {
        $settingModel = new SettingModel();
        $settings = $settingModel->all();
        $url = [
            'setting/update-all' => Yii::$app->apiurl->createAbsoluteUrl(['setting/update-all'])
        ];
        $settings = $settingModel->classifyByModule($settings);
        return $this->render('index', [
            'settings' => $settings,
            'url' => $url
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
    public function actionRadiolist(){
        $data = [
            'set_name' => 'web_02',
            'set_value' => 1,
            'set_value_type' => Setting::NUMBER,
            'set_des' => '爆炸威力',
            'set_module' => Setting::M_WEBSITE,
            'set_validators' => [
                ['number']
            ],
            'set_validators_params' => '',
            'set_widget' => SettingWidget::W_RADIOLIST,
            'set_widget_params' => [
                'map' => [
                    1 => '一级原子弹',
                    2 => '二级原子弹'
                ]
            ]
        ];
        $settingModel = new SettingModel();
        $result = $settingModel->create($data);
        if(!$result){
            console($settingModel->getOneError());
        }
    }
    public function actionRegion(){
        $data = [
            'set_name' => 'web_region',
            'set_value' => '1,2801,2827',
            'set_value_type' => Setting::STRING,
            'set_des' => '战略地点',
            'set_module' => Setting::M_WEBSITE,
            'set_validators' => [],
            'set_validators_params' => '',
            'set_widget' => SettingWidget::W_REGION,
            'set_widget_params' => [
            ]
        ];
        $settingModel = new SettingModel();
        $result = $settingModel->create($data);
        if(!$result){
            console($settingModel->getOneError());
        }
    }
    public function actionCheckbox(){
        $data = [
            'set_name' => 'web_01',
            'set_value' => 1,
            'set_value_type' => Setting::NUMBER,
            'set_des' => '打击目标',
            'set_module' => Setting::M_WEBSITE,
            'set_validators' => [
                ['number']
            ],
            'set_validators_params' => '',
            'set_widget' => SettingWidget::W_CHECKBOX,
            'set_widget_params' => [
                'map' => [
                    1 => '广东省',
                    2 => '福建省'
                ]
            ]
        ];
        $settingModel = new SettingModel();
        $result = $settingModel->create($data);
        if(!$result){
            console($settingModel->getOneError());
        }
    }
    public function actionDropdown(){
        $data = [
            'set_name' => 'web_area',
            'set_value' => 1,
            'set_value_type' => Setting::NUMBER,
            'set_des' => '攻击地区',
            'set_module' => Setting::M_WEBSITE,
            'set_validators' => [
                ['number']
            ],
            'set_validators_params' => '',
            'set_widget' => SettingWidget::W_DROPDOWN,
            'set_widget_params' => [
                'map' => [
                    1 => '广东省',
                    2 => '福建省'
                ]
            ]
        ];
        $settingModel = new SettingModel();
        $result = $settingModel->create($data);
        if(!$result){
            console($settingModel->getOneError());
        }
    }
    public function actionDatetime(){
        $data = [
            'set_name' => 'author_birthday',
            'set_value' => '2012-12-12 12:12:12',
            'set_value_type' => Setting::DATETIME,
            'set_des' => '作者生日',
            'set_module' => Setting::M_WEBSITE,
            'set_validators' => [
                ['datetime', 'format' => 'yyyy-MM-dd HH:mm:ss']
            ],
            'set_validators_params' => '',
            'set_widget' => SettingWidget::W_DATETIME,
            'set_widget_params' => ''
        ];
        $settingModel = new SettingModel();
        $result = $settingModel->create($data);
        if(!$result){
            console($settingModel->getOneError());
        }
    }

}
