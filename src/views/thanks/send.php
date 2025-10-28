<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\assets\UserAssets;

UserAssets::register($this);

$this->title = Module::t('Send thanks');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('https://cdn.jsdelivr.net/npm/vanilla-emoji-picker@0.2.4/dist/emojiPicker.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->registerJs("new EmojiPicker();");
?>
<div class="thanks-send">
    <div class="white-block">
        <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

        <?php $form = ActiveForm::begin(); ?>

        <?php if (!empty($id)) { $model->user_id = $id;?>
        <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>
        <?php } else { ?>
        <?= $form->field($model, 'user_id')->dropDownList(ArrayHelper::map(User::find()->all(), 'id', 'username'), ['prompt' => 'Выберите пользователя']) ?>
        <?php } ?>

        <?= $form->field($model, 'text')->textarea(['rows' => 6, 'data-emoji-picker'=>"true"]) ?>

        <div class="form-group">
            <?= Html::submitButton(Module::t('Send thanks'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
