<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;

$site_link = Yii::$app->urlManager->createAbsoluteUrl(['site/index']);
?>

<div class="password-set">
    <h2><?= Module::t('Hello')?>, <?= Html::encode($user->getName()) ?></h2>
    <p><?= Module::t('Your data for access to')?> <?= Html::a(Html::encode($site_link), $site_link) ?>:</p>
    <p><?= Module::t('Login') ?>: <?= Html::encode($user->username) ?></p>
    <p><?= Module::t('Password')?>: <?= Html::encode($password) ?></p>
</div>
