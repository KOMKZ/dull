<?php
namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class CKEditorAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins/ckeditor';
    public $css = [

    ];
    public $js = [
        'ckeditor.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

}
