<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\Module;

$this->title = Module::t('Send thanks');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('https://cdn.jsdelivr.net/npm/vanilla-emoji-picker@0.2.4/dist/emojiPicker.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->registerJs("new EmojiPicker();");
?>
<div class="thanks-send">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->dropDownList(ArrayHelper::map(User::find()->all(), 'id', 'username'), ['prompt' => 'Выберите пользователя']) ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 6, 'data-emoji-picker'=>"true"]) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Send thanks'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
