<?php
namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class Md5Asset extends AssetBundle
{
    public $sourcePath = '@common/assets/source';

    public $js = [
        'md5.js',
    ];
}
