<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use ZakharovAndrew\user\Module;

/** @var yii\web\View $this */
/** @var User $user */
/** @var UserDeputy[] $deputies */
/** @var User[] $availableUsers */

$this->title = Module::t('Deputies for {user}', ['user' => $user->name]);
$this->params['breadcrumbs'][] = ['label' => Module::t('Users'), 'url' => ['/user/user/index']];
$this->params['breadcrumbs'][] = ['label' => $user->name, 'url' => ['/user/user/profile', 'id' => $user->id]];
$this->params['breadcrumbs'][] = Module::t('Deputies');

?>
<div class="user-deputy-index">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><?= Module::t('Add Deputy') ?></h5>
                </div>
                <div class="card-body">
                    <?= Html::a(Module::t('Add Deputy'), ['create', 'user_id' => $user->id], ['class' => 'btn btn-success']) ?>
                    <?php if (\Yii::$app->user->identity->isAdmin()) {?>
                    <?= Html::a(Module::t('Users with Deputies'), ['list'], ['class' => 'btn btn-danger']) ?>
                    <?php } ?>
                    
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><?= Module::t('Deputy Statistics') ?></h5>
                </div>
                <div class="card-body">
                    <p><?= Module::t('Total deputies: {count}', ['count' => count($deputies)]) ?></p>
                    <p><?= Module::t('User is deputy for: {count} users', ['count' => count($user->getCurrentDeputyForUsers())]) ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($deputies)): ?>
        <div class="alert alert-info">
            <?= Module::t('No deputies found for this user.') ?>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?= Module::t('Deputy') ?></th>
                                <th><?= Module::t('Valid From') ?></th>
                                <th><?= Module::t('Valid To') ?></th>
                                <th><?= Module::t('Created By') ?></th>
                                <th><?= Module::t('Created At') ?></th>
                                <th><?= Module::t('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deputies as $deputy): ?>
                                <tr>
                                    <td>
                                        <?= Html::a(
                                            $deputy->deputyUser->name, 
                                            ['/user/user/view', 'id' => $deputy->deputy_user_id],
                                            ['target' => '_blank']
                                        ) ?>
                                        <br>
                                        <small class="text-muted"><?= $deputy->deputyUser->email ?></small>
                                    </td>
                                    <td><?= Yii::$app->formatter->asDate($deputy->valid_from) ?></td>
                                    <td>
                                        <?= $deputy->valid_to ? Yii::$app->formatter->asDate($deputy->valid_to) : 
                                            '<span class="text-muted">' . Module::t('No end date') . '</span>' ?>
                                    </td>
                                    <td>
                                        <?= $deputy->createdBy ? Html::a(
                                            $deputy->createdBy->name,
                                            ['/user/user/view', 'id' => $deputy->created_by],
                                            ['target' => '_blank']
                                        ) : '-' ?>
                                    </td>
                                    <td><?= Yii::$app->formatter->asDatetime($deputy->created_at) ?></td>
                                    <td>
                                        <?= Html::a(
                                            Module::t('Edit'),
                                            ['update', 'id' => $deputy->id],
                                            ['class' => 'btn btn-sm btn-outline-primary']
                                        ) ?>
                                        <?= Html::a(
                                            Module::t('Remove'),
                                            ['remove', 'id' => $deputy->id],
                                            [
                                                'class' => 'btn btn-sm btn-outline-danger',
                                                'data' => [
                                                    'confirm' => Module::t('Are you sure you want to remove this deputy?'),
                                                    'method' => 'post',
                                                ],
                                            ]
                                        ) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php
$js = <<<JS
    $(document).on('click', '.quick-add-deputy', function() {
        var userId = $(this).data('user-id');
        var deputyId = $(this).data('deputy-id');
        var deputyName = $(this).data('deputy-name');
        var button = $(this);
        
        if (confirm('Добавить ' + deputyName + ' в качестве заместителя?')) {
            $.post({
                url: '/user/user-deputy/quick-add',
                data: {
                    user_id: userId,
                    deputy_user_id: deputyId
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        button.hide();
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Произошла ошибка при добавлении заместителя.');
                }
            });
        }
    });
JS;

$this->registerJs($js);
?>