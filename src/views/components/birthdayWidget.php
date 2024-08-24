<?php
/* @var $this \yii\web\View */
/* @var $headerMessage string */
/* @var $users ZakharovAndrew\user\models\User[] */
?>

<div class="birthday-widget">
    <h3><?= htmlspecialchars($headerMessage) ?></h3>
    <ul>
        <?php foreach ($users as $user): ?>
            <li><?= htmlspecialchars($user->username) ?></li>
        <?php endforeach; ?>
    </ul>
</div>