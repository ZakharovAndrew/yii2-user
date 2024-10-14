<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;

$reset_link = Yii::$app->urlManager->createAbsoluteUrl(['/user/birthday-greeting/view']);
?>

<div class="password-set">
    <h1><?= Module::t('Happy Birthday')?>, <?= Html::encode($user->getName()) ?></h2>
    <p><?= Module::t("You've received one new birthday greeting.")?> </p>
    <p><a href="<?= $reset_link ?>" style="font-family: helvetica, 'helvetica neue', arial, verdana, sans-serif;font-size:18px;color: #FFFFFF;   border-style: solid;    border-color: #0077ff;border-width: 10px 30px 10px 30px;display: inline-block;background: #0077ff;border-radius: 25px;font-weight: bold;font-style: normal;line-height: 21.6px;width: auto;text-align: center;padding: 10px 30px;text-decoration: none"><?= Module::t('Go to greetings') ?></a></p>
</div>
