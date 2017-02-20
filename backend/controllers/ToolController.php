<?php
namespace backend\controllers;

use yii\helpers\VarDumper;
use common\base\Controller;
use Fukuball\Jieba\Jieba;
use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\JiebaAnalyse;


/**
 *
 */
class ToolController extends Controller
{
    public function actionWordSeg(){
        ini_set('memory_limit', '1024M');

        $result = [];
        Jieba::init(array('mode'=>'default','dict'=>'big'));
        Finalseg::init();


        return $this->render('word-seg', [
            'result' => VarDumper::export($tags)
        ]);
    }
}
