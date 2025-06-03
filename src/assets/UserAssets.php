<?php
/**
 * @link https://github.com/ZakharovAndrew/yii2-user
 * @copyright Copyright (c) 2024 Zakharov Andrey
 */

namespace ZakharovAndrew\user\assets;

use yii\web\AssetBundle;

class UserAssets extends AssetBundle
{
    public $sourcePath = '@vendor/zakharov-andrew/yii2-user/src/assets';

    public $css = [
        'css/style_v1.13.css',
    ];

    public $js = [
    //    'js/script.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        //'yii\bootstrap5\BootstrapAsset',
    ];
}