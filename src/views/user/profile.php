<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\models\UserSettingsConfig;
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
                <p><?= Module::t('Birthday'). ' : ' . (empty($model->birthday) ? Module::t('Not specified') : date('d.m.Y', strtotime($model->birthday))) ?></p>
                <p><?= Module::t('Sex'). ' : ' . User::getSexList()[$model->sex] ?? Module::t('Not specified') ?></p> 
                
            </div>
            <div class="profile-action-block">
                <?php if ($model->id == Yii::$app->user->id) {
                    echo Html::a(Module::t('Edit Profile'), ['edit-profile'], ['class' => 'btn btn-primary']);
                    
                    echo Html::a(Module::t('Appreciation'), ['thanks/view'], ['class' => 'btn btn-success']);
                } else { 
                    echo Html::a(Module::t('Appreciation'), ['thanks/view', 'id' => $model->id], ['class' => 'btn btn-success']);
                } ?>
                <?php if (!empty($model->phone)) {
                    echo '<p>'. Module::t('Phone'). ' : ' . $model->phone .'</p>';
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
                    //var_dump($setting->getValues());
                    $value = $setting->getUserSettingValue($model->id);
                    if ($setting->type == UserSettingsConfig::TYPE_CHECKBOX) {
                        echo $value == 1 ? Module::t('Yes') : Module::t('No');
                    } else if ($setting->type == UserSettingsConfig::TYPE_MULTI_SELECT_DROPDOWN && !empty($value)) {
                        $field_value = explode(',', $value);
                        $arr = [];
                        foreach ($field_value as $item_value) {
                            $arr[] = $setting->getValues()[$item_value];
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
</div>