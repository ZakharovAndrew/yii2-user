<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\widgets\BirthdayCalendarWidget;

$this->title = Module::t('Birthday Calendar');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="birthday-calendar-page">
    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>
    
    <?= BirthdayCalendarWidget::widget([
        'title' => Module::t('Upcoming Birthdays'),
    ]) ?>
</div>