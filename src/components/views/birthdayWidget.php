<?php
use ZakharovAndrew\user\assets\UserAssets;
use yii\helpers\Url;
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
                <a href="<?= Url::to(['/user/birthday-greeting/send', 'id' => $user->id]) ?>"><?php if ($useAvatars) {?>
                <img src="<?= !$user->getAvatarUrl() ?
                                Yii::$app->assetManager->getAssetUrl(UserAssets::register($this), 'images/default-avatar.png') :
                                $user->getAvatarUrl()
                            ?>" alt="Avatar">
                <?php } ?>
                <?= htmlspecialchars($user->name) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>