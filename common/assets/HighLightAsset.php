<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class HighLightAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/source';
    public $css = [
    ];
    public $js = [
        'highlight.js',
    ];
    public $depends = [

    ];
}
