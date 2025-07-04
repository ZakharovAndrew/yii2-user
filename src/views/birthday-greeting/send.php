<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\Module;

$this->title = Module::t('Send birthday congratulations');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('https://cdn.jsdelivr.net/npm/vanilla-emoji-picker@0.2.4/dist/emojiPicker.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->registerJs("new EmojiPicker();");
?>
<div class="birthday-greeting-send">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6, 'data-emoji-picker'=>"true"]) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Send congratulations'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
