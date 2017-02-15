<?php
namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AdSelectAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/source';
    public $js = [
        "advance-select.js",
    ];
    public $depends = [
        // 'backend\assets\JqAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset'
    ];
}
