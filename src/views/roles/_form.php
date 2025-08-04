<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\Roles;

use ZakharovAndrew\user\assets\UserAssets;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\Roles $model */
/** @var yii\widgets\ActiveForm $form */
?>

<style>
    .checkbox-input-menu .menu-item {
    display: flex;
    align-items: center;
    margin: 10px 0;
    padding: 8px;
    border-radius: 4px;
    background: #f5f8fa;
}

.checkbox-input-menu .menu-checkbox {
    margin-right: 10px;
}

.checkbox-input-menu .menu-input {
    margin-left: 10px;
    width: 100%;
    display: inline-block;
    background: #e4ecf1;
}

.checkbox-input-menu .menu-label {
    margin-left: 5px;
    cursor: pointer;
    width: 280px;
}

.checkbox-input-menu .menu-group {
    margin-bottom: 20px;
    border: 1px solid #eee;
    padding: 15px;
    border-radius: 5px;
}

.checkbox-input-menu .group-title {
    margin-top: 0;
    color: #333;
    font-size: 1.1em;
}
</style>

<div class="roles-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="white-block">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'code')->textInput() ?>
        
        <?= $form->field($model, 'function_to_get_all_subjects')->textInput(['maxlength' => true]) ?>
        
        <label><?= Module::t('Parameters')?></label>

<?= ZakharovAndrew\user\widgets\CheckboxMenuWithInput::widget([
    'menu' => Roles::getAllowedParametersList(),
    'model' => $model,
    'attribute' => 'parameters',
    'selectedData' => $model->parameters ?? []
]) ?>

        

        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
