<?php
namespace backend\controllers;

use Yii;
use yii\helpers\Url;
use common\base\AdminController;
use common\models\order\TransModel;
use common\models\order\tables\Trans;
use common\models\payment\tables\Payment;

class TransController extends AdminController
{
    public function actionList(){
        $transModel = new TransModel();
        list($provider, $pagination) = $transModel->getProvider();
        return $this->render('list', [
            'provider' => $provider,
            'map' => Trans::getValidConsts(),
        ]);
    }

}
