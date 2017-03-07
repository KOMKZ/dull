<?php
namespace api\controllers;

use Yii;
use common\models\open\OpenModel;
use common\base\ApiController;
use yii\helpers\Html;



/**
 *
 */
class OpenController extends ApiController{
    public function actionGetRegion(){
        $parent_id = Yii::$app->request->get('parent_id');
        $result = OpenModel::getRegion($parent_id);
        return $this->succ($result);
    }
}
