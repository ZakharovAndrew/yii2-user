<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;

/** @var yii\web\View $this */
/** @var use ZakharovAndrew\user\app\models\User $model */

$this->title = 'Telegram';
$this->params['breadcrumbs'][] = ['label' => Module::t('Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <p><?= Module::t('You can receive notifications from a telegram bot') ?>
        
    <?php if (!empty($model->telegram_code)) {?>
    <p><?= Module::t('Send the bot a message') ?>:</p>
    <div class="alert alert-success">/register <?= $model->telegram_code ?></div>
    <?php } ?>

</div>
