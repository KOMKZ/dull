<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\loopc\LoopcModel;

/**
 *
 */
class DemoController extends Controller
{
    public $enableCsrfValidation = false;

    public function action1(){
        $model = new LoopcModel();
        $data = [];
        $result = $model->createContainer($data);
    }

}
