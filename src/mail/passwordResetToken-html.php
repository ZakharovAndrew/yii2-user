<?php
 
use Yii;
use yii\helpers\Html;
use ZakharovAndrew\user\Module;


$reset_link = Yii::$app->urlManager->createAbsoluteUrl(['/user/user/reset-password', 'token' => $user->password_reset_token]);

?>
 
<div class="password-reset">
    <p><?= Module::t('Hello')?>, <?= Html::encode($user->getName()) ?></p>
    <p><?= Module::t('To reset your password, follow the link') ?>:</p>
    <p><?= Html::a(Html::encode($reset_link), $reset_link) ?></p>
</div>
