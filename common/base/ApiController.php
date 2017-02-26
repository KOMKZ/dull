<?php
namespace common\base;

use Yii;
use common\base\Controller;


class ApiController extends Controller
{
    public $enableCsrfValidation = false;
    private function getRes(){
        return [
            'code' => null,
            'data' => null,
            'message' => null
        ];
    }

    private function asRes($data){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $data;
    }
    public function notfound($error){
        return $this->error(404, $error ? $error : '数据不存在');
    }
    public function succ($data = null){
        $res = $this->getRes();
        $res['data'] = $data;
        $res['code'] = 0;
        $res['message'] = '成功';
        return $this->asRes($res);
    }
    public function error($code, $message){
        $res = $this->getRes();
        $res['data'] = null;
        $res['code'] = empty($code) ? 1 : $code;
        $res['message'] = $message;
        return $this->asRes($res);
    }

}
