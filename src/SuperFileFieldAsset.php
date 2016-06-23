<?php

namespace floor12\superfilefield;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SuperFileFieldAsset extends AssetBundle
{

    public $publishOptions = [
        'forceCopy' => true,
    ];
    public $sourcePath = '@vendor/floor12/yii2-super-file-field/assets/';
    public $css = [
        'superfilefield.css'
    ];
    public $js = [
        'superfilefield.js',
        'SimpleAjaxUploader.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\jui\JuiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
