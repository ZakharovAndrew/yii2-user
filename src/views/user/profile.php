<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\models\UserSettingsConfig;
use ZakharovAndrew\user\models\Friendship;
use ZakharovAndrew\user\assets\UserAssets;
use yii\helpers\Url;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\User $model */

$this->title = Module::t('Profile');
?>
<style>
    .user-avatar-delete {
        display: none;
        box-shadow: 0 6px 14px 12px rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        width: 22px;
        height: 22px;
        background: #fff;
        text-align: center;
        font-size: 14px;
        position: absolute;
        bottom: 13px;
        right: -10px;
        cursor: pointer;
        text-decoration: none;
    }
    .avatar-block:hover .user-avatar-delete {
        display: block;
    }
    .profile-action-block {
        text-align: right;
    }
    .profile-action-block .btn {
        margin-bottom: 8px;
    }
    .profile-action-block .btn-success {
        background-color: #8bc34a;
        border-color: #8bc34a;
    }
    .profile-action-block .btn-success:hover {
        background-color: #73a934;
        border-color: #73a934;
    }
</style>

<div class="user-profile">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <div class="white-block">
        <div class="profile-main">
            <div class="avatar-block">
                <?php if ($model->id == Yii::$app->user->id) {?>
                <a href="<?= Url::to(['upload-avatar']) ?>">
                <?php } ?>
                <img src="<?= !$model->getAvatarUrl() ?
                                Yii::$app->assetManager->getAssetUrl(UserAssets::register($this), 'images/default-avatar.png') :
                                $model->getAvatarUrl()
                            ?>" alt="Avatar">
                <?php if ($model->id == Yii::$app->user->id) {?>
                </a><a href="<?= Url::to(['delete-avatar']) ?>" class='user-avatar-delete' data-bs-toggle="tooltip" title="Remove delete">X</a>
                <?php }?>
            </div>
            <div style="-webkit-flex: 0 1 100%;   flex: 0 1 100%;">
                <h3><?= $model->name ?></h3>
                <p><?= Module::t('City'). ' : ' . $model->city ?></p>
                <p><?= Module::t('Birthday'). ' : ' . (empty($model->birthday) ? Module::t('Not specified') : date('d.m.Y', strtotime($model->birthday))) ?>
                    <?php
                // Happy Birthday button
                if ($model->isBirthdayToday() && $model->id != Yii::$app->user->id) {
                    echo' <a href="'.Url::to(['/user/birthday-greeting/send', 'id' => $model->id]).'" title="'.Module::t('Send birthday congratulations').'">🎁</a>';
                }
                ?></p>
                <p><?= Module::t('Sex'). ' : ' . User::getSexList()[$model->sex] ?? Module::t('Not specified') ?></p> 
                
            </div>
            <div class="profile-action-block">
                <?php if ($model->id == Yii::$app->user->id) {
                    echo Html::a(Module::t('Edit Profile'), ['edit-profile'], ['class' => 'btn btn-primary']);
                } else if  (Yii::$app->user->identity->hasRole('admin')) {
                    echo Html::a(Module::t('Edit Profile'), ['edit-profile', 'id' => $model->id], ['class' => 'btn btn-primary']);
                }
                if ($model->id == Yii::$app->user->id) {    
                    echo Html::a(Module::t('Appreciation'), ['thanks/view'], ['class' => 'btn btn-success']);
                } else { 
                    echo Html::a(Module::t('Appreciation'), ['thanks/view', 'id' => $model->id], ['class' => 'btn btn-success']);
                } ?>
                <?php if (!empty($model->phone)) {
                    echo '<p>'. Module::t('Phone'). ' : ' . $model->phone .'</p>';
                } ?>
                
                <!-- Friendship button -->
                <?php if ($model->id != Yii::$app->user->id): ?>
                    <?php
                    $currentUser = Yii::$app->user->identity;
                    $friendshipStatus = $currentUser->getFriendshipStatus($model->id);
                    ?>
                    
                    <?php if (!$friendshipStatus): ?>
                        <!-- No friendship - show Add Friend button -->
                        <a href="<?= Url::to(['/user/friend/send-request', 'id' => $model->id]) ?>" 
                           class="btn btn-primary btn-friendship" 
                           data-method="post"
                           title="<?= Module::t('Add to Friends') ?>">
                            <i class="glyphicon glyphicon-plus"></i> <?= Module::t('Add Friend') ?>
                        </a>
                        
                    <?php elseif ($friendshipStatus['status'] == Friendship::STATUS_PENDING): ?>
                        <!-- Pending request -->
                        <?php if ($friendshipStatus['is_sent_by_me']): ?>
                            <!-- Sent by current user -->
                            <button class="btn btn-info btn-friendship" disabled title="<?= Module::t('Request Sent') ?>">
                                <i class="glyphicon glyphicon-time"></i> <?= Module::t('Request Sent') ?>
                            </button>
                            <a href="<?= Url::to(['/user/friend/cancel', 'id' => $model->id]) ?>" 
                               class="btn btn-warning btn-xs" 
                               data-method="post"
                               data-confirm="<?= Module::t('Are you sure you want to cancel this friend request?') ?>"
                               title="<?= Module::t('Cancel Request') ?>">
                                <i class="glyphicon glyphicon-remove"></i>
                            </a>
                        <?php else: ?>
                            <!-- Received from this user -->
                            <button class="btn btn-warning btn-friendship" disabled title="<?= Module::t('Request Received') ?>">
                                <i class="glyphicon glyphicon-time"></i> <?= Module::t('Request Received') ?>
                            </button>
                            <div style="margin-top: 5px;">
                                <a href="<?= Url::to(['/user/friend/accept', 'id' => $model->id]) ?>" 
                                   class="btn btn-success btn-xs" 
                                   data-method="post"
                                   title="<?= Module::t('Accept') ?>">
                                    <i class="glyphicon glyphicon-ok"></i> <?= Module::t('Accept') ?>
                                </a>
                                <a href="<?= Url::to(['/user/friend/reject', 'id' => $model->id]) ?>" 
                                   class="btn btn-danger btn-xs" 
                                   data-method="post"
                                   data-confirm="<?= Module::t('Are you sure you want to reject this friend request?') ?>"
                                   title="<?= Module::t('Reject') ?>">
                                    <i class="glyphicon glyphicon-remove"></i> <?= Module::t('Reject') ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                    <?php elseif ($friendshipStatus['status'] == Friendship::STATUS_ACCEPTED): ?>
                        <!-- Already friends -->
                        <button class="btn btn-success btn-friendship" disabled title="<?= Module::t('Friends') ?>">
                            <i class="glyphicon glyphicon-ok"></i> <?= Module::t('Friends') ?>
                        </button>
                        <div style="margin-top: 5px;">
                            <a href="<?= Url::to(['/user/friend/remove', 'id' => $model->id]) ?>" 
                               class="btn btn-danger btn-xs" 
                               data-method="post"
                               data-confirm="<?= Module::t('Are you sure you want to remove this friend?') ?>"
                               title="<?= Module::t('Remove Friend') ?>">
                                <i class="glyphicon glyphicon-remove"></i> <?= Module::t('Remove') ?>
                            </a>
                        </div>
                        
                    <?php elseif ($friendshipStatus['status'] == Friendship::STATUS_REJECTED): ?>
                        <!-- Rejected request -->
                        <button class="btn btn-danger btn-friendship" disabled title="<?= Module::t('Request Rejected') ?>">
                            <i class="glyphicon glyphicon-remove"></i> <?= Module::t('Rejected') ?>
                        </button>
                        <?php if ($friendshipStatus['is_sent_by_me']): ?>
                            <!-- Current user sent the rejected request -->
                            <div style="margin-top: 5px;">
                                <a href="<?= Url::to(['/user/friend/send-request', 'id' => $model->id]) ?>" 
                                   class="btn btn-primary btn-xs" 
                                   data-method="post"
                                   title="<?= Module::t('Send Again') ?>">
                                    <i class="glyphicon glyphicon-refresh"></i> <?= Module::t('Send Again') ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                    <?php elseif ($friendshipStatus['status'] == Friendship::STATUS_BLOCKED): ?>
                        <!-- Blocked user -->
                        <button class="btn btn-warning btn-friendship" disabled title="<?= Module::t('User Blocked') ?>">
                            <i class="glyphicon glyphicon-ban-circle"></i> <?= Module::t('Blocked') ?>
                        </button>
                        <div style="margin-top: 5px;">
                            <a href="<?= Url::to(['/user/friend/unblock', 'id' => $model->id]) ?>" 
                               class="btn btn-primary btn-xs" 
                               data-method="post"
                               data-confirm="<?= Module::t('Are you sure you want to unblock this user?') ?>"
                               title="<?= Module::t('Unblock User') ?>">
                                <i class="glyphicon glyphicon-ok-circle"></i> <?= Module::t('Unblock') ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Link to friend management -->
                    <?php if ($friendshipStatus && $friendshipStatus['status'] != Friendship::STATUS_BLOCKED): ?>
                        <div style="margin-top: 10px;">
                            <a href="<?= Url::to(['/user/friend/index']) ?>" 
                               class="btn btn-link btn-xs">
                                <i class="glyphicon glyphicon-user"></i> <?= Module::t('Manage Friends') ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <!-- For own profile - link to friends page -->
                    <div style="margin-top: 10px;">
                        <a href="<?= Url::to(['/user/friend/index']) ?>" 
                           class="btn btn-primary">
                            <i class="glyphicon glyphicon-user"></i> 
                            <?= Module::t('My Friends') ?> 
                            <?php if ($model->friends_count > 0): ?>
                                <span class="badge"><?= $model->friends_count ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endif; ?>
                 
            </div>
        </div>
          
    </div>
    
    <div class="white-block">
        <div class="profile-additional">
            <?php foreach ($settings as $setting) { ?>
                <?php 
                if (!empty($setting->hidden_for_roles)) {
                    if (Yii::$app->user->identity->hasRole($setting->getRolesForHiddenList())) {
                        continue;
                    }
                }
                ?>
                <div class="profile-user-settings">
                    <label><?= $setting->title . ' : ' ?></label>
                    <?php 
                    //var_dump($setting->getValues());
                    $value = $setting->getUserSettingValue($model->id);
                    if ($setting->type == UserSettingsConfig::TYPE_CHECKBOX) {
                        echo $value == 1 ? Module::t('Yes') : Module::t('No');
                    } else if ($setting->type == UserSettingsConfig::TYPE_MULTI_SELECT_DROPDOWN && !empty($value)) {
                        $field_value = explode(',', $value);
                        $arr = [];
                        foreach ($field_value as $item_value) {
                            if (isset($setting->getValues()[$item_value])) {
                                $arr[] = $setting->getValues()[$item_value];
                            }
                        }
                        
                        echo implode(', ', $arr);
                    } else if (!empty($setting->values) && !empty($value)) {
                        echo $setting->getValues()[$value] ?? $value;
                    } else {
                        echo $value;
                    }?>
                </div>
            <?php } ?>
        </div>
    </div>
    
    <?php if ($model->id == Yii::$app->user->id && !empty(Yii::$app->getModule('user')->telegramBotLink)) {?>
    <div class="white-block">
        <p><?= Module::t('You can receive notifications from a telegram bot') ?> <a href="<?= Yii::$app->getModule('user')->telegramBotLink ?>" target="_blank"><?= str_replace(['https://t.me/', 'http://t.me/'], '@', Yii::$app->getModule('user')->telegramBotLink) ?></a>

        <?php if (empty($model->telegram_id)) {?>
        <p><?= Module::t('Send the bot a message') ?>:</p>
        <div style="display: flex"><div class="alert alert-success">/register <?= $model->telegram_code ?></div><div class="alert">or go to <a href="<?= Yii::$app->getModule('user')->telegramBotLink ?>/register <?= $model->telegram_code ?>" target="_blank">link</a></div></div>
        <?php } else { ?>
        <p><a href="<?= Url::to(['unlink-telegram']) ?>" class="btn btn-danger"><?= Module::t('Unlink your account to the Telegram Bot') ?></a></p>
        <?php } ?>
    </div>
    <?php } ?>
    
    <?php if (Yii::$app->user->identity->hasRole('admin')) { ?>
    <div class="white-block">
        <label>Роли пользователя:</label>
        <div>
            <?= $this->render('_userRoles', ['model' => $model]); ?>
        </div>
    </div>
    <?php } ?>
</div>