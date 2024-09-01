<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\User;
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
                <p><?= Module::t('Birthday'). ' : ' . (empty($model->birthday) ? Module::t('Not specified') : date('d.m.Y', strtotime($model->birthday))) ?></p>
                <p><?= Module::t('Sex'). ' : ' . User::getSexList()[$model->sex] ?? Module::t('Not specified') ?></p> 
            </div>
            <div class="profile-action-block">
                <?php if ($model->id == Yii::$app->user->id) {
                    echo Html::a(Module::t('Edit Profile'), ['edit-profile'], ['class' => 'btn btn-primary']);
                } ?>
            </div>
        </div>
          
    </div>
    
    <div class="white-block">
        <div class="profile-additional">
            <?php foreach ($settings as $setting) {?>
                <div class="profile-user-settings">
                    <label><?= $setting->title . ' : ' ?></label>
                    <?php 
                    $value = $setting->getUserSettingValue($model->id);
                    if (!empty($setting->values) && !empty($value)) {
                        echo $setting->getValues()[$value] ?? null;
                    } else {
                        echo $value;
                    }?>
                </div>
            <?php } ?>
        </div>
    </div>
    
    <?php if ($model->id == Yii::$app->user->id) {?>
    <div class="white-block">
        <p><?= Module::t('You can receive notifications from a telegram bot') ?> <a href="<?= Yii::$app->getModule('user')->telegramBotLink ?>" target="_blank"><?= Yii::$app->getModule('user')->telegramBotLink ?></a>

        <?php if (empty($model->telegram_id)) {?>
        <p><?= Module::t('Send the bot a message') ?>:</p>
        <div class="alert alert-success">/register <?= $model->telegram_code ?></div></div>
        <?php } else { ?>
        <p><a href="<?= Url::to(['unlink-telegram']) ?>" class="btn btn-danger"><?= Module::t('Unlink your account to the Telegram Bot') ?></a></p>
        <?php } ?>
    </div>
    <?php } ?>
</div>