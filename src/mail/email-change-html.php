<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;

$reset_link = Yii::$app->urlManager->createAbsoluteUrl([
    '/user/user/set-new-email',
    'username' => $user->username,
    'email' => $model->new_email,
    'key' => md5($model->new_email.Yii::$app->name)
]);
?>

<div class="email-new">
    <p><?= Module::t('Hello')?>, <?= Html::encode($user->getName()) ?></p>
    <p><?= Module::t('To confirm your email, please follow the link') ?>:</p>
    <p><?= Html::a(Html::encode($reset_link), $reset_link) ?></p>
</div>
