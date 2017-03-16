<?php
namespace common\base;

use Yii;
use common\base\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use common\models\notify\NotifyModel;
use yii\helpers\Url;


class AdminController extends Controller
{
    public function behaviors()
    {
        // todo 权限控制这样弄 不好拆卸
        // 比如哪天我就不需要安装权限控制了，这里不够灵活
        return YII_ENV != 'dev' ?[
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        // 'roles' => [$this->route]
                        'roles' => ['@']
                    ]
                ]
            ]
        ] : [];
    }

    public $enableCsrfValidation = false;

    public function error($code, $message){
        Yii::$app->session->setFlash('error', $message . ':' . $code);
    }

    public function notfound(){
        throw new NotFoundHttpException();
    }

    public function succ($message = null){
        Yii::$app->session->setFlash('success', $message ? $message : '成功');
    }

    public function render($view, $params = [])
    {
        $viewComponent = $this->getView();
        if(!Yii::$app->user->isGuest){
            $viewComponent = Yii::$app->view;
            $viewComponent->params['notifications'] = NotifyModel::getLatestUserMsg(Yii::$app->user->getId(), 5, '0');
            $viewComponent->params['notifications_count'] = count($viewComponent->params['notifications']);
            $viewComponent->params['notifications_view_all'] = Url::toRoute(['notify/index']);
        }else{
            $viewComponent->params['notifications'] = [];
            $viewComponent->params['notifications_count'] = 0;
            $viewComponent->params['notifications_view_all'] = '#';
        }
        $content = $viewComponent->render($view, $params, $this);
        return $this->renderContent($content);
    }


}
