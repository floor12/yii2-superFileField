<?php

namespace floor12\superfilefield;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SimpleAjaxUploaderAsset extends AssetBundle
{


    public $sourcePath = '@vendor/floor12/yii2-super-file-field/assets/';
    public $js = [
        'SimpleAjaxUploader.min.js'
    ];
}
