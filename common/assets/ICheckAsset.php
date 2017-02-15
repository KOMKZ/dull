<?php
namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class ICheckAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins/iCheck';
    public $css = [
        'all.css',
    ];
    public $js = [
        'icheck.min.js'
    ];
    public $depends = [
        // 'backend\assets\JqAsset'
        'yii\web\YiiAsset',
    ];

}
