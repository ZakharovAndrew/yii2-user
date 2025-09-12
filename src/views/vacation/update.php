<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\VacationType;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\user\models\Vacation */
/* @var $form yii\widgets\ActiveForm */

$this->title = Module::t('Update Vacation: {name}', ['name' => $model->user->name]);
$this->params['breadcrumbs'][] = ['label' => Module::t('Vacations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Module::t('Vacation Details'), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('Update');

// Получаем доступные типы отпусков
$vacationTypes = VacationType::getTypesList();

?>
<div class="vacation-update">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><?= Module::t('Vacation Information') ?></h5>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'type_id')->dropDownList(
                        $vacationTypes,
                        [
                            'prompt' => Module::t('Select vacation type...'),
                            'disabled' => !$model->canBeEdited(),
                            'class' => 'form-control form-select'
                        ]
                    )->label(Module::t('Vacation Type')) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'days_count')->textInput([
                        'readonly' => true,
                        'class' => 'form-control bg-light'
                    ])->label(Module::t('Days Count')) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'start_date')->input('date', [
                        'class' => 'form-control',
                        'disabled' => !$model->canBeEdited()
                    ])->label(Module::t('Start Date')) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'end_date')->input('date', [
                        'class' => 'form-control',
                        'disabled' => !$model->canBeEdited()
                    ])->label(Module::t('End Date')) ?>
                </div>
            </div>

            <?= $form->field($model, 'comment')->textarea([
                'rows' => 4,
                'placeholder' => Module::t('Enter comments about vacation...')
            ])->label(Module::t('Comments')) ?>

            <?php if (Yii::$app->user->identity->isAdmin()): ?>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'status')->dropDownList(
                        $model->getStatusList(),
                        ['prompt' => Module::t('Select status...'), 'class' => 'form-control form-select']
                    )->label(Module::t('Status')) ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
                <?= Html::a(Module::t('Cancel'), ['view', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php if (!$model->canBeEdited()): ?>
    <div class="alert alert-warning mt-4">
        <h5><?= Module::t('Attention') ?></h5>
        <p><?= Module::t('You can only edit vacation requests with status "Requested".') ?></p>
        <p><?= Module::t('Current status: {status}', [
            'status' => Html::tag('strong', $model->getStatusLabel()['label'])
        ]) ?></p>
        
        <?php if ($model->status == Vacation::STATUS_APPROVED && $model->start_date <= date('Y-m-d')): ?>
        <p class="mb-0">
            <?= Module::t('This vacation has already started and cannot be edited.') ?>
        </p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title"><?= Module::t('Current Vacation Details') ?></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong><?= Module::t('Employee') ?>:</strong><br>
                    <?= Html::encode($model->user->name) ?>
                </div>
                <div class="col-md-4">
                    <strong><?= Module::t('Current Type') ?>:</strong><br>
                    <?= $model->type ? Html::encode($model->type->name) : 'N/A' ?>
                </div>
                <div class="col-md-4">
                    <strong><?= Module::t('Current Period') ?>:</strong><br>
                    <?= Yii::$app->formatter->asDate($model->start_date) ?> - 
                    <?= Yii::$app->formatter->asDate($model->end_date) ?>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <strong><?= Module::t('Days') ?>:</strong><br>
                    <?= $model->days_count ?>
                </div>
                <div class="col-md-4">
                    <strong><?= Module::t('Status') ?>:</strong><br>
                    <?php $status = $model->getStatusLabel(); ?>
                    <span class="badge <?= $status['class'] ?>">
                        <?= $status['label'] ?>
                    </span>
                </div>
                <div class="col-md-4">
                    <strong><?= Module::t('Created') ?>:</strong><br>
                    <?= Yii::$app->formatter->asDatetime($model->created_at) ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
// JavaScript для автоматического расчета дней и валидации
$this->registerJs(<<<JS
$(document).ready(function() {
    // Функция для расчета количества дней
    function calculateDays() {
        var startDate = new Date($('#vacation-start_date').val());
        var endDate = new Date($('#vacation-end_date').val());
        
        if (startDate && endDate && startDate <= endDate) {
            var timeDiff = endDate.getTime() - startDate.getTime();
            var daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
            $('#vacation-days_count').val(daysDiff);
        } else {
            $('#vacation-days_count').val(0);
        }
    }

    // Слушаем изменения дат
    $('#vacation-start_date, #vacation-end_date').change(function() {
        calculateDays();
        validateDates();
    });

    // Валидация дат
    function validateDates() {
        var startDate = new Date($('#vacation-start_date').val());
        var endDate = new Date($('#vacation-end_date').val());
        var today = new Date();
        
        // Сбрасываем предыдущие ошибки
        $('.field-vacation-start_date, .field-vacation-end_date').removeClass('has-error');
        $('.help-block-error').remove();

        if (startDate && endDate) {
            if (startDate > endDate) {
                showError('vacation-end_date', 'Дата окончания должна быть после даты начала');
                return false;
            }
            
            if (startDate < today) {
                showError('vacation-start_date', 'Дата начала не может быть в прошлом');
                return false;
            }
        }
        
        return true;
    }

    function showError(field, message) {
        var fieldElement = $('.field-' + field);
        fieldElement.addClass('has-error');
        fieldElement.append('<div class="help-block help-block-error">' + message + '</div>');
    }

    // Инициализация при загрузке
    calculateDays();
    
    // Предотвращаем отправку формы при ошибках
    $('form').on('submit', function(e) {
        if (!validateDates()) {
            e.preventDefault();
            // Прокручиваем к первой ошибке
            $('.has-error').first().closest('.form-group')[0].scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    });
});
JS
);

// CSS стили
$this->registerCss(<<<CSS
.vacation-update .card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.vacation-update .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.vacation-update .bg-light {
    background-color: #f8f9fa !important;
}

.vacation-update .help-block-error {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.vacation-update .has-error .form-control {
    border-color: #dc3545;
}

.vacation-update .alert {
    border-left: 4px solid #ffc107;
}

.vacation-update .badge {
    font-size: 0.875rem;
    padding: 0.4em 0.6em;
}
CSS
);