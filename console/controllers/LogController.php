<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;

/**
 * 日志工具
 */
class LogController extends Controller{
    public function action1(){
        throw new \Exception('hello world');
    }
    public function action2(){
        $path = Yii::getAlias('@app/runtime/logs/app.log');
        $h = fopen($path, 'r');
        fseek($h, 11486);
        $contents = '';
        while (!feof($h)) {
          $contents .= fread($h, 8192);
        }
        fclose($h);
    }
}
