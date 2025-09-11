<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\Module;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\user\models\VacationRequestForm */
/* @var $vacation ZakharovAndrew\user\models\Vacation */

$this->title = Module::t('Process Vacation Request');
$this->params['breadcrumbs'][] = ['label' => Module::t('Vacations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Module::t('Vacation Details'), 'url' => ['view', 'id' => $vacation->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="vacation-process-request">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title"><?= Module::t('Vacation Details') ?></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong><?= Module::t('Employee') ?>:</strong> <?= Html::encode($vacation->user->name) ?></p>
                    <p><strong><?= Module::t('Vacation Type') ?>:</strong> <?= Html::encode($vacation->type->name) ?></p>
                    <p><strong><?= Module::t('Period') ?>:</strong> 
                        <?= Yii::$app->formatter->asDate($vacation->start_date) ?> - 
                        <?= Yii::$app->formatter->asDate($vacation->end_date) ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong><?= Module::t('Days') ?>:</strong> <?= $vacation->days_count ?></p>
                    <p><strong><?= Module::t('Status') ?>:</strong> 
                        <span class="badge badge-<?= $vacation->getStatusLabel()['class'] ?>">
                            <?= $vacation->getStatusLabel()['label'] ?>
                        </span>
                    </p>
                    <p><strong><?= Module::t('Requested at') ?>:</strong> 
                        <?= Yii::$app->formatter->asDatetime($vacation->created_at) ?>
                    </p>
                </div>
            </div>
            
            <?php if ($vacation->comment): ?>
                <div class="alert alert-info mt-3">
                    <strong><?= Module::t('User Comment') ?>:</strong><br>
                    <?= nl2br(Html::encode($vacation->comment)) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><?= Module::t('Process Request') ?></h5>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'action')->radioList([
                'approve' => Module::t('Approve Vacation'),
                'reject' => Module::t('Reject Vacation'),
            ], [
                'item' => function($index, $label, $name, $checked, $value) {
                    $color = $value === 'approve' ? 'success' : 'danger';
                    return Html::tag('div', 
                        Html::radio($name, $checked, [
                            'value' => $value,
                            'label' => $label,
                            'labelOptions' => ['class' => "btn btn-outline-$color"]
                        ]), 
                        ['class' => 'btn-group-toggle', 'data-toggle' => 'buttons']
                    );
                }
            ]) ?>

            <?= $form->field($model, 'comment')->textarea([
                'rows' => 4,
                'placeholder' => Module::t('Enter reason for rejection...')
            ]) ?>

            <?= $form->field($model, 'notify_user')->checkbox([
                'label' => Module::t('Send notification to user')
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton(Module::t('Process Request'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Module::t('Cancel'), ['view', 'id' => $vacation->id], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

<?php
// JavaScript для показа/скрытия комментария
$this->registerJs(<<<JS
    $(document).ready(function() {
        toggleCommentField();
        
        $('input[name="VacationRequestForm[action]"]').change(function() {
            toggleCommentField();
        });
        
        function toggleCommentField() {
            var action = $('input[name="VacationRequestForm[action]"]:checked').val();
            if (action === 'reject') {
                $('#vacationrequestform-comment').closest('.form-group').show();
            } else {
                $('#vacationrequestform-comment').closest('.form-group').hide();
            }
        }
    });
JS
);