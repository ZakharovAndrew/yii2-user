<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\assets\UserAssets;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\User $model */

$this->title = Module::t('Profile');
?>
<style>
    .profile-main {
        display: flex; gap:20px;
        flex: 0 0 100%;
    }
    @media (max-width: 728px) {
        .profile-main {
            flex-direction: column;
        }
        .avatar-block img {
            width: 100%;
            height:auto;
        }
    }
    .profile-user-settings {
        display: flex;
    }
    .profile-user-settings label {
        font-weight: 700;
        width: 200px;
        margin-bottom: 15px; 
        color:#a4a9af
    }
    .profile-action-clock .btn {text-wrap: nowrap; border-radius: 12px;}
</style>

<div class="user-profile">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>
    
    

    <div class="profile-block">
        <div class="profile-main">
            <div class="avatar-block">
                <img src="<?= !$model->getAvatarUrl() ?
                                Yii::$app->assetManager->getAssetUrl(UserAssets::register($this), 'images/default-avatar.png') :
                                $model->getAvatarUrl()
                            ?>" alt="Avatar">
            </div>
            <div style="-webkit-flex: 0 1 100%;   flex: 0 1 100%;">
                <h3><?= $model->name ?></h3>
                <p><?= Module::t('City'). ' : ' . $model->city ?></p>
                <p><?= Module::t('Birthday'). ' : ' . (empty($model->birthday) ? 'Не указан' : date('d.m.Y', strtotime($model->birthday))) ?></p>
                <p><?= Module::t('Sex'). ' : ' . User::getSexList()[$model->sex] ?? 'Не указан' ?></p> 
            </div>
            <div class="profile-action-clock">
                <?php if ($model->id == Yii::$app->user->id) {
                    echo Html::a(Module::t('Edit Profile'), ['edit-profile'], ['class' => 'btn btn-primary']);
                } ?>
            </div>
        </div>
          
    </div>
    
    <div class="profile-block">
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
    <div class="profile-block">
        <p><?= Module::t('You can receive notifications from a telegram bot') ?> <a href="<?= Yii::$app->getModule('user')->telegramBotLink ?>" target="_blank"><?= Yii::$app->getModule('user')->telegramBotLink ?></a>

        <?php if (!empty($model->telegram_code)) {?>
        <p><?= Module::t('Send the bot a message') ?>:</p>
        <div class="alert alert-success">/register <?= $model->telegram_code ?></div></div>
        <?php } ?>
    </div>
    <?php } ?>
</div>