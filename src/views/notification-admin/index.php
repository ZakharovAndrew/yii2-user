<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\Roles;

/* @var $this yii\web\View */
/* @var $groups ZakharovAndrew\user\models\NotificationGroup[] */

use ZakharovAndrew\user\assets\UserAssets;
UserAssets::register($this);

$bootstrapVersion = Yii::$app->getModule('user')->bootstrapVersion;
$classModal = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Modal";

$this->title = Module::t('Notifications');
$this->params['breadcrumbs'][] = $this->title;

$plus_svg = '<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1em" viewBox="0 0 459.325 459.325" xml:space="preserve"><path d="M459.319 229.668c0 22.201-17.992 40.193-40.205 40.193H269.85v149.271c0 22.207-17.998 40.199-40.196 40.193-11.101 0-21.149-4.492-28.416-11.763-7.276-7.281-11.774-17.324-11.769-28.419l-.006-149.288H40.181c-11.094 0-21.134-4.492-28.416-11.774C4.501 250.817.006 240.769.006 229.668 0 207.471 17.992 189.475 40.202 189.475h149.267V40.202C189.469 17.998 207.471 0 229.671 0c22.192.006 40.178 17.986 40.19 40.187v149.288h149.282c22.196.012 40.165 17.996 40.176 40.193z"/></svg>';
$pencil_svg = '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M498 142l-46 46c-5 5-13 5-17 0L324 77c-5-5-5-12 0-17l46-46c19-19 49-19 68 0l60 60c19 19 19 49 0 68zm-214-42L22 362 0 484c-3 16 12 30 28 28l122-22 262-262c5-5 5-13 0-17L301 100c-4-5-12-5-17 0zM124 340c-5-6-5-14 0-20l154-154c6-5 14-5 20 0s5 14 0 20L144 340c-6 5-14 5-20 0zm-36 84h48v36l-64 12-32-31 12-65h36v48z"></path></svg>';
$trash_svg = '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"></path></svg>';
?>
<style>
    .notification-name {
        padding-left: 25px !important;
    }
    .notification-group td {
        /*background-color:#E1F5FE*/
    }
    .table-notification tr:hover td{background-color: #f8f8f8}
    .table-notification td {border-bottom: 1px dashed #cdcdcd;vertical-align: middle !important;}
    .edit-notification-button {
        background-color: #0d99ff1a;
        border-color:#0d99ff1a;
        color:#0d99ff;
    }
    .edit-notification-button:hover, .edit-notification-button:active {
        background-color: #0d99ff !important;
        border-color:#0d99ff !important;
        color:#fff !important;
    }
    .delete-notification-button {
        background-color: #ffdede;
        border-color: #ffdede;
        color: #FF5E5E;
    }
    .delete-notification-button:hover, .delete-notification-button:active {
        background-color: #FF5E5E;
        border-color: #FF5E5E;
        color:#fff;
    }
    #notification-roles label {width:49%}
    
</style>
<?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

<p>
    <?= Html::button(Module::t('Add Group'), [
        'class' => 'btn btn-success',
        'id' => 'add-group-button',
    ]) ?>
</p>

<div class="white-block">
    <table class="table table-notification">
        
        <tbody>
            <?php foreach ($groups as $group): ?>
                <tr class="notification-group">
                    <td><b><?= Html::encode($group->name) ?></b></td>
                    <td><?= Html::encode($group->description) ?></td>
                    <td align="right">
                        <?= Html::a($plus_svg, '#', [
                            'class' => 'btn btn-sm btn-default btn-light add-notification-button',
                            'data-group-id' => $group->id,
                            'title' => Module::t('Add Notification'),
                        ]) ?>
                        <?= Html::a($pencil_svg, '#', [
                            'class' => 'btn btn-sm btn-default btn-light edit-group-button',
                            'data-group-id' => $group->id,
                            'data-group-name' => $group->name,
                            'data-group-description' => $group->description,
                            'title' => 'Edit Group'
                        ]) ?>
                        <?= Html::a($trash_svg, ['delete-group', 'id' => $group->id], [
                            'class' => 'btn btn-sm btn-default btn-light',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this group?',
                                'method' => 'post',
                            ],
                            'title' => Module::t('Delete Group')
                        ]) ?>
                    </td>
                </tr>
                <?php foreach ($group->notifications as $notification): ?>
                <tr id="row-<?= $notification->id ?>">
                    <td colspan="2" class="notification-name" title="<?= $notification->description ?>"><?= Html::encode($notification->name) ?></td>
                    <td align="right">
                        <?= Html::a($pencil_svg, '#', [
                            'class' => 'btn btn-sm btn-info edit-notification-button',
                            'data-notification-id' => $notification->id,
                            'data-notification-name' => $notification->name,
                            'data-notification-description' => $notification->description,
                            'data-notification-code-name' => $notification->code_name,
                            'data-notification-function-to-call' => $notification->function_to_call,
                            'data-notification-roles' => json_encode(array_column($notification->roles, 'id')),
                            'title' => Module::t('Edit Notification'),
                        ]) ?>
                        <?= Html::a($trash_svg, '#', [
                            'class' => 'btn btn-sm btn-danger delete-notification-button',
                            'data-notification-id' => $notification->id,
                            'title' => Module::t('Delete Notification'),
                        ]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
// Modal for adding a new group
$classModal::begin([
    ($bootstrapVersion==3 ? 'header' : 'title') => '<h2>'.Module::t('Add Notification Group').'</h2>',
    'id' => 'add-group-modal',
]);

$form = ActiveForm::begin(['id' => 'add-group-form']);

echo $form->field($model, 'name')->textInput(['maxlength' => true]);
echo $form->field($model, 'description')->textarea(['rows' => 6]);

echo '<div class="form-group">';
echo Html::submitButton('Create', ['class' => 'btn btn-success']);
echo '</div>';

ActiveForm::end();

$classModal::end();
?>

<?php
// Modal for adding a new notification to a group
$classModal::begin([
    ($bootstrapVersion == 3 ? 'header' : 'title') => '<h2>'.Module::t('Add Notification').'</h2>',
    'id' => 'add-notification-modal',
]);
$form = ActiveForm::begin(['id' => 'add-notification-form']);
echo $form->field($modelNotification, 'name')->textInput(['maxlength' => true]);
echo $form->field($modelNotification, 'description')->textarea(['rows' => 4]);
echo $form->field($modelNotification, 'code_name')->textInput(['maxlength' => 255]);
echo $form->field($modelNotification, 'function_to_call')->textInput(['maxlength' => 255]);

// Поле для выбора ролей
echo $form->field($modelNotification, 'roles')->checkboxList(
    \yii\helpers\ArrayHelper::map(Roles::find()->all(), 'id', 'title')
);

echo Html::hiddenInput('notification_group_id', '', ['id' => 'notification-group-id']);
echo '<div class="form-group">';
echo Html::submitButton('Create', ['class' => 'btn btn-success']);
echo '</div>';
ActiveForm::end();
$classModal::end();
?>

<?php
// Modal for editing a group
$classModal::begin([
    ($bootstrapVersion == 3 ? 'header' : 'title') => '<h2>Edit Group</h2>',
    'id' => 'edit-group-modal',
]);
$form = ActiveForm::begin(['id' => 'edit-group-form']);
echo $form->field($model, 'name')->textInput(['maxlength' => true]);
echo $form->field($model, 'description')->textarea(['rows' => 6]);
echo Html::hiddenInput('groupId', '', ['id' => 'edit-group-id']);
echo '<div class="form-group">';
echo Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']);
echo '</div>';
ActiveForm::end();
$classModal::end();
?>

<?php
// Modal for editing a notification
$classModal::begin([
    ($bootstrapVersion == 3 ? 'header' : 'title') => '<h2>'.Module::t('Edit Notification').'</h2>',
    'id' => 'edit-notification-modal',
]);
$form = ActiveForm::begin(['id' => 'edit-notification-form']);

echo $form->field($modelNotification, 'name')->textInput(['maxlength' => true]);
echo $form->field($modelNotification, 'description')->textarea(['rows' => 4]);
echo $form->field($modelNotification, 'code_name')->textInput(['maxlength' => 255]);
echo $form->field($modelNotification, 'function_to_call')->textInput(['maxlength' => 255]);

// Поле для выбора ролей
echo $form->field($modelNotification, 'roles')->checkboxList(
    \yii\helpers\ArrayHelper::map(Roles::find()->all(), 'id', 'title')
);

echo Html::hiddenInput('notificationId', '', ['id' => 'edit-notification-id']);
echo '<div class="form-group">';
echo Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']);
echo '</div>';
ActiveForm::end();
$classModal::end();
?>

<?php
$script = <<< JS
    $('#add-group-button').click(function() {
        $('#add-group-modal').modal('show');
    });

    $('#add-group-form').on('beforeSubmit', function(e) {
        e.preventDefault(); // Prevent default form submission
        $.ajax({
            url: 'create-group-ajax', // URL to the action that handles AJAX request
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    // Reload the page to show the new group
                    location.reload();
                } else {
                    // Handle errors
                    $.each(response.errors, function(key, val) {
                        $('#add-group-form').yiiActiveForm('addError', key, val);
                    });
                }
            }
        });
        return false; // Prevent default form submission
    });
        
    $('.add-notification-button').click(function() {
        var groupId = $(this).data('group-id');
        $('#notification-group-id').val(groupId);
        $('#add-notification-modal').modal('show');
    });

    $('#add-notification-form').on('beforeSubmit', function(e) {
        e.preventDefault(); // Prevent default form submission
        var formData = $(this).serialize();
        var groupId = $('#notification-group-id').val();

        $.ajax({
            url: 'create-notification-ajax?groupId=' + groupId,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    $.each(response.errors, function(key, val) {
                        $('#add-notification-form').yiiActiveForm('addError', key, val);
                    });
                }
            }
        });
        return false; // Prevent default form submission
    });

    $('.edit-group-button').click(function() {
        var groupId = $(this).data('group-id');
        var groupName = $(this).data('group-name');
        var groupDescription = $(this).data('group-description');

        $('#edit-group-id').val(groupId);
        $('#edit-group-form input[name="NotificationGroup[name]"]').val(groupName);
        $('#edit-group-form textarea[name="NotificationGroup[description]"]').val(groupDescription);

        $('#edit-group-modal').modal('show');
    });

    $('#edit-group-form').on('beforeSubmit', function(e) {
        e.preventDefault(); // Prevent default form submission
        var formData = $(this).serialize();
        var groupId = $('#edit-group-id').val();

        $.ajax({
            url: 'edit-group-ajax?id=' + groupId,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    $.each(response.errors, function(key, val) {
                        $('#edit-group-form').yiiActiveForm('addError', key, val);
                    });
                }
            }
        });
        return false; // Prevent default form submission
    });
        
    $('.edit-notification-button').click(function() {
        var notificationId = $(this).data('notification-id');
        var notificationName = $(this).data('notification-name');
        var notificationDescription = $(this).data('notification-description');
        var codeName = $(this).data('notification-code-name');
        var functionToCall = $(this).data('notification-function-to-call');
        var roles = JSON.parse($(this).attr('data-notification-roles') || []);
        console.log('as', roles, $(this).data('notification-roles'));

        $('#edit-notification-id').val(notificationId);
        $('#edit-notification-form input[name="Notification[name]"]').val(notificationName);
        $('#edit-notification-form textarea[name="Notification[description]"]').val(notificationDescription);
        $('#edit-notification-form input[name="Notification[code_name]"]').val(codeName);
        $('#edit-notification-form input[name="Notification[function_to_call]"]').val(functionToCall);

        $('#edit-notification-form input[name="Notification[roles][]"]').prop('checked', false);
        $.each(roles, function(index, roleId) {
            $('#edit-notification-form input[name="Notification[roles][]"][value="' + roleId + '"]').prop('checked', true);
        });

        $('#edit-notification-modal').modal('show');
    });

    $('#edit-notification-form').on('beforeSubmit', function(e) {
        e.preventDefault(); // Prevent default form submission
        var formData = $(this).serialize();
        var notificationId = $('#edit-notification-id').val();

        $.ajax({
            url: 'edit-notification-ajax?id=' + notificationId,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Обновляем данные уведомления в DOM
                    var notificationRow = $('#row-' + response.notification.id);
                    notificationRow.find('.notification-name').text(response.notification.name);
                    notificationRow.find('.notification-name').attr('title', response.notification.description);
                    notificationRow.find('.edit-notification-button').attr('data-notification-roles', response.notification.roles);
                    
                    notificationRow.find('.edit-notification-button').attr('data-notification-roles', JSON.stringify(response.notification.roles));
                    notificationRow.find('.edit-notification-button').data('notification-roles', JSON.stringify(response.notification.roles));

                    $('#edit-notification-modal').modal('hide');
                } else {
                    $.each(response.errors, function(key, val) {
                        $('#edit-notification-form').yiiActiveForm('addError', key, val);
                    });
                }
            }
        });
        return false; // Prevent default form submission
    });

    $('.delete-notification-button').click(function() {
        var notificationId = $(this).data('notification-id');
        var notificationRow = $(this).closest('tr');

        if (confirm('Are you sure you want to delete this notification?')) {
            $.ajax({
                url: 'delete-notification-ajax?id=' + notificationId,
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        notificationRow.remove();
                    } else {
                        alert('Failed to delete notification.');
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the notification.');
                }
            });
        }
    });
JS;
$this->registerJs($script);
?>
