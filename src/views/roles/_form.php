<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\Module;

use ZakharovAndrew\user\assets\UserAssets;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\Roles $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="roles-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="white-block">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'code')->textInput() ?>

        <?= $form->field($model, 'parameters')->textarea(['rows' => 3]) ?>

        <?= $form->field($model, 'function_to_get_all_subjects')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
