<?php
use ZakharovAndrew\user\Module;

/* @var $vacation \ZakharovAndrew\user\models\Vacation */
/* @var $approver \ZakharovAndrew\user\models\User */
/* @var $comment string */
?>

<h2><?= Module::t('Your vacation request has been reviewed') ?></h2>

<p><?= Module::t('Hello {name},', ['name' => $vacation->user->name]) ?></p>

<p><?= Module::t('Unfortunately, your vacation request has been rejected by {approver}.', [
    'approver' => $approver->name
]) ?></p>

<?php if (!empty($comment)): ?>
<div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <h3><?= Module::t('Reason for rejection') ?>:</h3>
    <p><?= nl2br(Html::encode($comment)) ?></p>
</div>
<?php endif; ?>

<div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <h3><?= Module::t('Vacation Details') ?>:</h3>
    <p><strong><?= Module::t('Type') ?>:</strong> <?= $vacation->type->name ?></p>
    <p><strong><?= Module::t('Period') ?>:</strong> 
        <?= Yii::$app->formatter->asDate($vacation->start_date) ?> - 
        <?= Yii::$app->formatter->asDate($vacation->end_date) ?>
    </p>
</div>

<p><?= Module::t('Please contact your manager to discuss alternative dates.') ?></p>

<p><?= Module::t('Best regards,') ?><br>
<?= Yii::$app->name ?></p>