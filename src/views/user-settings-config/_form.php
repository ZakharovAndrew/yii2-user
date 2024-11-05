<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\models\UserSettingsConfig;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\assets\UserAssets;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\UserSettingsConfig $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-settings-config-form white-block">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(UserSettingsConfig::getTypeOfSettings(), ['prompt' => '', 'class' => 'form-control form-select']) ?>

    <?= $form->field($model, 'access_level')->dropDownList(UserSettingsConfig::getAccessLevel(), ['prompt' => '', 'class' => 'form-control form-select']) ?>

    <?= $form->field($model, 'values')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
