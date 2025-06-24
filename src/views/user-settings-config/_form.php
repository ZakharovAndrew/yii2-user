<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\models\UserSettingsConfig;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\assets\UserAssets;
use ZakharovAndrew\user\models\Roles;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\UserSettingsConfig $model */
/** @var yii\widgets\ActiveForm $form */

// CSS/JS Select2
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
// init Select2
$this->registerJs(<<<JS
    $('.select2').select2({
        placeholder: "Выберите...",
        allowClear: true
    });
JS
);
?>
<style>
    .user-settings-config-form .select2-container--default .select2-selection--single,
    .user-settings-config-form .select2-container .select2-selection--multiple
    {
        background: #f5f8fa;
        border: none;
    }
</style>

<div class="user-settings-config-form white-block">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(UserSettingsConfig::getTypeOfSettings(), ['prompt' => '', 'class' => 'form-control form-select']) ?>
    
    <?= $form->field($model, 'hidden_for_roles')->dropDownList(Roles::getRolesList(), ['prompt' => '', 'multiple' => 'multiple', 'class' => 'form-control form-select select2']) ?>

    <?= $form->field($model, 'access_level')->dropDownList(UserSettingsConfig::getAccessLevel(), ['prompt' => '', 'class' => 'form-control form-select']) ?>

    <?= $form->field($model, 'values')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
