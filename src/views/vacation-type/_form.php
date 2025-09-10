<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\Module;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\user\models\VacationType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vacation-type-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="white-block">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'color')->input('color') ?>
            </div>
        </div>

        <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'is_paid')->checkbox() ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'is_active')->checkbox() ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'requires_approval')->checkbox() ?>
            </div>
            
        </div>

        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'max_days_per_year')->textInput(['type' => 'number']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'min_days_per_request')->textInput(['type' => 'number']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'max_days_per_request')->textInput(['type' => 'number']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'priority')->textInput(['type' => 'number']) ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>