<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\models\Roles;
use ZakharovAndrew\user\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\UserRoles $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-roles-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>
    
    <?= $form->field($model, 'role_id')->dropDownList(Roles::getRolesList()) ?>
    
    <?= $form->field($model, 'subject_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
