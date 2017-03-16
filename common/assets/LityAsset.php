<?php
namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class LityAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/source/lity-2.2.2/dist';
    public $css = [
        'lity.min.css',
    ];
    public $js = [
        'lity.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

}
