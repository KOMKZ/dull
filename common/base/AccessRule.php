<?php
namespace common\base;

use yii\base\Component;
/**
 *
 */
class AccessRule extends Component
{
    public $denyCallback;
    public $allowRoutes = [];
    public function allows($action, $user, $request){
        $route = $action->controller->route;
        if(in_array($route, $allowRoutes)){
            return true;
        }
    }
}
