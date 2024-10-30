<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\assets\UserAssets;
use ZakharovAndrew\user\models\User;
use yii\widgets\ActiveForm;

UserAssets::register($this);

$this->title = Module::t('Import users');

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\ImportForm $model */
?>
<div class="user-import">
    
    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>
    
    <?= $result ?>
    
    <div class="white-block">
        
        <p><?= Module::t('Select CSV file and status to import users') ?></p>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <?= $form->field($model, 'csvFile')->fileInput()->label(false) ?>
        
        <?= $form->field($model, 'separator')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'status')->dropDownList(User::getStatusList()) ?>

        <div class="form-group">
            <?= Html::submitButton(Module::t('Import'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end() ?>
    </div>
</div>
