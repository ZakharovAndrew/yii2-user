<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\Module;

use ZakharovAndrew\user\assets\UserAssets;
UserAssets::register($this);
/* @var $this yii\web\View */
/* @var $groups array */
/* @var $userId int */

$this->title = Module::t('Notifications');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .notification-name {
        padding-left: 25px !important;
    }
</style>
<?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

<div class="white-block">
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th>Email</th>
                <th>Telegram</th>
                <th>Push</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($groups as $group): ?>
                <tr>
                    <td colspan="2"><b><?= Html::encode($group->name) ?></b></td>
                    <td align="center">
                        <label>
                            <?= Html::checkbox("master-email-{$group->id}", false, [
                                'class' => 'master-checkbox',
                                'data-group-id' => $group->id,
                                'data-type' => 'email',
                            ]) ?> Select All Email
                        </label>
                    </td>
                    <td align="center">
                        <label>
                                <?= Html::checkbox("master-telegram-{$group->id}", false, [
                                    'class' => 'master-checkbox',
                                    'data-group-id' => $group->id,
                                    'data-type' => 'telegram',
                                ]) ?> Select All Telegram
                        </label>
                    </td>
                    <td align="center">
                        <label>
                            <?= Html::checkbox("master-push-{$group->id}", false, [
                                'class' => 'master-checkbox',
                                'data-group-id' => $group->id,
                                'data-type' => 'push',
                            ]) ?> Select All Push
                        </label>
                    </td>
                </tr>
                <?php foreach ($group->notifications as $notification): ?>
                    <tr>
                        <td colspan="2" class="notification-name" title="<?= $notification->description ?>"><?= Html::encode($notification->name) ?></td>
                        <td align="center">
                            <?php
                            $settings = $notification->getUserSetting($userId);
                            $settings = $settings ? $settings : new \ZakharovAndrew\user\models\UserNotificationSetting([
                                'user_id' => $userId,
                                'notification_id' => $notification->id,
                                'send_email' => false,
                                'send_telegram' => false,
                                'send_push' => false,
                            ]);
                            ?>
                            <label>
                                <?= Html::checkbox("settings[$notification->id][send_email]", $settings->send_email ?? false, [
                                    'class' => 'notification-setting',
                                    'data-notification-id' => $notification->id,
                                    'data-user-id' => $userId,
                                    'data-type' => 'email',
                                ]) ?>
                            </label>
                        </td>
                        <td align="center">
                            <label>
                                <?= Html::checkbox("settings[$notification->id][send_telegram]", $settings->send_telegram ?? false, [
                                    'class' => 'notification-setting',
                                    'data-notification-id' => $notification->id,
                                    'data-user-id' => $userId,
                                    'data-type' => 'telegram',
                                ]) ?>
                            </label>
                        </td>
                        <td align="center">
                            <label>
                                <?= Html::checkbox("settings[$notification->id][send_push]", $settings->send_push ?? false, [
                                    'class' => 'notification-setting',
                                    'data-notification-id' => $notification->id,
                                    'data-user-id' => $userId,
                                    'data-type' => 'push',
                                ]) ?>
                            </label>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<?php
$script = <<< JS
$(document).on('change', '.notification-setting', function() {
    var userId = $(this).data('user-id');
    var notificationId = $(this).data('notification-id');
    var type = $(this).data('type');
    var value = $(this).is(':checked');

    $.ajax({
        url: 'save-notification-setting',
        type: 'POST',
        data: {
            user_id: userId,
            notification_id: notificationId,
            type: type,
            value: value,
        },
        success: function(response) {
            if (response.success) {
                console.log('Setting saved successfully');
            } else {
                console.error('Failed to save setting');
            }
        },
        error: function() {
            console.error('An error occurred while saving the setting');
        }
    });
});
JS;
$this->registerJs($script);
?>