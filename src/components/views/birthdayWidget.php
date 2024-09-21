<?php
use ZakharovAndrew\user\assets\UserAssets;
UserAssets::register($this);

/* @var $this \yii\web\View */
/* @var $headerMessage string */
/* @var $users ZakharovAndrew\user\models\User[] */
?>

<div class="birthday-widget">
    <h3><?= htmlspecialchars($headerMessage) ?></h3>
    <ul>
        <?php foreach ($users as $user): ?>
            <li>
                <?php if ($useAvatars) {?>
                <img src="<?= !$user->getAvatarUrl() ?
                                Yii::$app->assetManager->getAssetUrl(UserAssets::register($this), 'images/default-avatar.png') :
                                $user->getAvatarUrl()
                            ?>" alt="Avatar">
                <?php } ?>
                <?= htmlspecialchars($user->name) ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>