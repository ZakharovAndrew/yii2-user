<?php
use ZakharovAndrew\user\Module;

/* @var $vacation \ZakharovAndrew\user\models\Vacation */
/* @var $approver \ZakharovAndrew\user\models\User */
?>

<h2><?= Module::t('Your vacation has been approved!') ?></h2>

<p><?= Module::t('Hello {name},', ['name' => $vacation->user->name]) ?></p>

<p><?= Module::t('Your vacation request has been approved by {approver}.', [
    'approver' => $approver->name
]) ?></p>

<div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <h3><?= Module::t('Vacation Details') ?>:</h3>
    <p><strong><?= Module::t('Type') ?>:</strong> <?= $vacation->type->name ?></p>
    <p><strong><?= Module::t('Period') ?>:</strong> 
        <?= Yii::$app->formatter->asDate($vacation->start_date) ?> - 
        <?= Yii::$app->formatter->asDate($vacation->end_date) ?>
    </p>
    <p><strong><?= Module::t('Duration') ?>:</strong> <?= $vacation->days_count ?> <?= Module::t('days') ?></p>
</div>

<p><?= Module::t('Please make sure to complete all your tasks before the vacation.') ?></p>

<p><?= Module::t('Best regards,') ?><br>
<?= Yii::$app->name ?></p>