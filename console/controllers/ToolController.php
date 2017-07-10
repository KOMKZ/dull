<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\FileHelper;

/**
 * todo 使用render file来改写
 */
class ToolController extends Controller{
    public function actionCreateModel(){
        $modelFolder = trim(Console::prompt("Enter folder model located:", ['required' => true]));
        $modelFolderPath = $this->getModelBasedir() . '/' . $modelFolder;
        if(is_file($modelFolderPath)){
            echo "error:{$modelFolderPath} is a file\n";
            exit();
        }
        if(!is_dir($modelFolderPath)){
            FileHelper::createDirectory($modelFolderPath);
            FileHelper::createDirectory($modelFolderPath . '/tables' );
        }
        $modelName = Console::prompt("Enter model name:");
        $modelTable = Console::prompt("Enter tabel model name:");
        if(!empty($modelName)){
            $modelFile = $modelFolderPath  . '/' . $modelName . '.php';
            file_put_contents($modelFile, strtr($this->getFileTpl('model_class'), [
                '%model_folder%' => $modelFolder,
                '%model_name%' => $modelName
            ]));
        }
        if(!empty($modelTable)){
            $modelTableFile = $modelFolderPath  . '/tables/' . $modelTable . '.php';
            file_put_contents($modelTableFile, strtr($this->getFileTpl('table_class'), [
                '%model_folder%' => $modelFolder,
                '%table_name%' => $modelTable
            ]));
        }
    }

    protected function getModelBasedir(){
        return Yii::getAlias('@common/models');
    }
    protected function getFileTpl($name){
        $tpls = [
            'model_class' => 'tool/model_class',
            'table_class' => 'tool/table_class'
        ];
        return file_get_contents(Yii::getAlias("@console/initdata/" . $tpls[$name]));
    }
}
