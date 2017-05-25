<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\file\FileModel;

/**
 * 定时任务
 */
class CronController extends Controller{
    /**
     * 清理临时文件
     * @return [type] [description]
     */
    public function actionClearTmpFile(){
        $fileModel = new FileModel();
        $tmpFileIds = $fileModel->getTmpFileIds();
        $succDelIds = [];
        foreach ($tmpFileIds as $value) {
            $result = $fileModel->deleteOneFile(['f_id' => $value]);
            if($result){
                $succDelIds[] = $value;
                echo "删除成功\n";
            }else{
                // 删除失败？
                echo "删除失败\n";
            }
        }
        $fileModel->savePrimaryIds($succDelIds);
    }
}
