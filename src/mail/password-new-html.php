<?php

use yii\helpers\Html;
use yii\helpers\Url;


$reset_link = Yii::$app->urlManager->createAbsoluteUrl(['site/index']);


?>

<div class="password-set">
    <p>Здравствуйте, <?= Html::encode($user->getName()) ?></p>
    <p>Ваши данные для доступа к <?= Html::a(Html::encode($reset_link), $reset_link) ?>:</p>
    <p>Логин: <?= Html::encode($user->username) ?></p>
    <p>Пароль: <?= Html::encode($password) ?></p>
    <p>Рекомендуем сменить Ваш пароль</p>
</div>
