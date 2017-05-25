<?php
use yii\base\Event;
use yii\web\User;
use common\models\user\UserEventHandler;


Yii::setAlias('@root', dirname(dirname(__DIR__)));
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@OSS', '@common/helpers/alisdk/OSS');
Yii::setAlias('@wxpay', '@common/helpers/wxpay/src');
require_once(Yii::getAlias('@common/helpers/alipayment2/AopSdk.php'));



Event::on(User::className(), User::EVENT_AFTER_LOGIN, [UserEventHandler::className(), 'handleAfterUserLogin']);
