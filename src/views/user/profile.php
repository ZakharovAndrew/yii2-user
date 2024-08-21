<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\User;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\User $model */

$this->title = Module::t('Profile');
?>
<style>
    .white-box {
        border-radius: 12px;
        padding:15px;
        background: #fff;
        margin-bottom: 20px;
    }
    .white-box .alert {
        margin-bottom: 0 
    }
</style>

<div class="user-profile">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>
    
    <?php if ($model->id == Yii::$app->user->id) {?>
    <p>
        <?= Html::a(Module::t('Edit Profile'), ['edit-profile'], ['class' => 'btn btn-primary']) ?>
    </p>
    <?php } ?>

    <h3><?= $model->name ?></h3>
    <p><?= Module::t('City'). ' : ' . $model->city ?></p>
    <p><?= Module::t('Birthday'). ' : ' . (empty($model->birthday) ? 'Не указан' : date('d.m.Y', strtotime($model->birthday))) ?></p>
    <p><?= Module::t('Sex'). ' : ' . User::getSexList()[$model->sex] ?? 'Не указан' ?></p>

    <?php foreach ($settings as $setting) {?>
        <p><?= $setting->title . ' : ' . $setting->getUserSettingValue($model->id) ?></p>
    <?php } ?>
    
    <?php if ($model->id == Yii::$app->user->id) {?>
    <div class="white-box">
        <p><?= Module::t('You can receive notifications from a telegram bot') ?> <a href="<?= Yii::$app->getModule('user')->telegramBotLink ?>" target="_blank"><?= Yii::$app->getModule('user')->telegramBotLink ?></a>

        <?php if (!empty($model->telegram_code)) {?>
        <p><?= Module::t('Send the bot a message') ?>:</p>
        <div class="alert alert-success">/register <?= $model->telegram_code ?></div></div>
        <?php } ?>
    </div>
    <?php } ?>
</div>