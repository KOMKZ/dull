<?php
namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class WUAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/source/webuploader';
    public $css = [
        'webuploader.css',
        // 'app/style.css',
    ];
    public $js = [
        'webuploader.js',
        'app/upload.js'
    ];
    public $depends = [
        'common\assets\Md5Asset',
        'yii\web\YiiAsset',
    ];
}
