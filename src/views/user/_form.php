<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\models\UserSettingsConfig;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\assets\UserAssets;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); 
    if (!empty($model->birthday)) {
        $model->birthday = date('Y-m-d', strtotime($model->birthday));
    }
    ?>

    <div class="white-block">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?php if (Yii::$app->user->identity->hasRole('admin')) {?>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'status')->dropDownList(User::getStatusList()) ?>
            </div>
        </div>
        <?php } ?>

        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'birthday')->input('date') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'sex')->dropDownList(User::getSexList(), ['class' => 'form-control form-select']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
    
    <div class="white-block">
        <?php foreach ($settings as $setting) {?>
            <div class="form-group">
                <label><?= $setting->title ?></label>
                <?php
                if ($setting->type == UserSettingsConfig::TYPE_STRING && !empty($setting->getValues())) {
                    echo Html::dropDownList( $setting->code, $setting->getUserSettingValue($model->id ?? 0), $setting->getValues(), [
                            'id' => 'settings-'.$setting->code,
                            'class' => 'form-control form-select',
                            'prompt' => ''
                        ]);
                } else if ($setting->type == UserSettingsConfig::TYPE_CHECKBOX) {
                    echo Html::checkbox($setting->code, $setting->getUserSettingValue($model->id ?? 0));
                } else {
                    // determine the type
                    $inputType = 'text';
                    if ($setting->type == UserSettingsConfig::TYPE_TIME) {
                        $inputType = 'time';
                    } else if ($setting->type == UserSettingsConfig::TYPE_DATE) {
                        $inputType = 'date';
                    }
                    echo Html::input($inputType, $setting->code, $setting->getUserSettingValue($model->id ?? 0), ['id' => 'settings-'.$setting->code, 'class' => 'form-control']);
                }?>
            </div>
        <?php } ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
