<?php
namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class DateTimePickerAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/source/datetime-picker';

    public $css = [
        "DateTimePicker.css",
    ];
    public $js = [
        'DateTimePicker.js'
    ];
    public $depends = [
        // 'backend\assets\JqAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset'
    ];

}
