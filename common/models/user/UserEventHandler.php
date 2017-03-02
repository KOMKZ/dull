<?php
namespace common\models\user;

use Yii;
use yii\base\Object;
use common\models\notify\NotifyModel;

/**
 *
 */
class UserEventHandler extends Object
{
    public static function handleAfterUserLogin($event){
        // alia_pacocha
        $notifyModel = new NotifyModel();
        $notifyModel->pullUserMsg(Yii::$app->user->getId());
    }
}
