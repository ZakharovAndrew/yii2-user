<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\models\Roles;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\assets\UserAssets;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\UserRoles $model */
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

echo $this->render('_js');
?>
<style>
    .user-roles-form .select2-container--default .select2-selection--single,
    .user-roles-form .select2-container .select2-selection--multiple
    {
        background: #f5f8fa;
        border: none;
    }
    
</style>

<div class="user-roles-form white-block">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>
    
    <?= $form->field($model, 'role_id')->dropDownList(Roles::getRolesList(), ['id' => 'role_id', 'prompt' => '', 'class' => 'form-control form-select select2']) ?>
    
    <?= $form->field($model, 'subject_id')->textInput(['maxlength' => true, 'id' => 'subject_id']) ?>
    
    <div id="role_subject_group" class="form-group" style="display: none">
        <label class="control-label" for="role_subject"><?= Module::t('Subject of the role') ?></label>
        <select id="role_subject" class="form-control form-select"></select>
    </div>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
