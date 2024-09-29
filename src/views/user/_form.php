<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\Module;

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

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'birthday')->input('date') ?>

    <?= $form->field($model, 'status')->dropDownList(User::getStatusList()) ?>

    <?= $form->field($model, 'sex')->dropDownList(User::getSexList()) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
