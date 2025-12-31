<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;

?>

<div class="email-verification">
    <p><?= Module::t('Hello')?>, <?= Html::encode($user->getName()) ?></p>
    <p>Your registration code:</p>

    <p><font size="large" style="letter-spacing: 8px;"><?= $user->email_verification_code?></font></p>
</div>
