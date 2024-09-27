<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\Module;

$this->title = Module::t('Send thanks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="thanks-send">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->dropDownList(ArrayHelper::map(User::find()->all(), 'id', 'username'), ['prompt' => 'Выберите пользователя']) ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Send thanks'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>