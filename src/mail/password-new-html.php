<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;

$reset_link = Yii::$app->urlManager->createAbsoluteUrl(['site/index']);
?>

<div class="password-set">
    <p><?= Module::t('Hello')?>, <?= Html::encode($user->getName()) ?></p>
    <p><?= Module::t('Your data for access to')?> <?= Html::a(Html::encode($reset_link), $reset_link) ?>:</p>
    <p><?= Module::t('Login') ?>: <?= Html::encode($user->username) ?></p>
    <p><?= Module::t('Password')?>: <?= Html::encode($password) ?></p>
    <p><?= Module::t('We recommend changing your password') ?></p>
</div>
