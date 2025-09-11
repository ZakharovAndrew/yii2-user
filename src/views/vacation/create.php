<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\VacationType;

use ZakharovAndrew\user\assets\UserAssets;
UserAssets::register($this);

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\user\models\Vacation */
/* @var $form yii\widgets\ActiveForm */
/* @var $vacationTypes array */

$this->title = Module::t('Request Vacation');
$this->params['breadcrumbs'][] = ['label' => Module::t('My Vacations'), 'url' => ['my']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="vacation-create">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <div class="white-block vacation-form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'type_id')->dropDownList(
                    $vacationTypes,
                    [
                        'prompt' => Module::t('Select vacation type...'),
                        'class' => 'form-control form-select'
                    ]
                ) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'start_date')->input('date') ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'end_date')->input('date') ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'days_count')->textInput([
                    'readonly' => true,
                    'value' => $model->days_count ?: 0
                ]) ?>
            </div>
        </div>

        <?= $form->field($model, 'comment')->textarea(['rows' => 4]) ?>

    </div>
    
    <div class="form-group">
        <?= Html::submitButton(Module::t('Request Vacation'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Module::t('Cancel'), ['my'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>

<?php
// JavaScript для автоматического расчета дней при изменении дат
$this->registerJs(<<<JS
    $(document).on('change', '#vacation-start_date, #vacation-end_date', function() {
        var startDate = new Date($('#vacation-start_date').val());
        var endDate = new Date($('#vacation-end_date').val());
        
        if (startDate && endDate && startDate <= endDate) {
            var timeDiff = endDate.getTime() - startDate.getTime();
            var daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
            $('#vacation-days_count').val(daysDiff);
        } else {
            $('#vacation-days_count').val(0);
        }
    });
JS
);
?>