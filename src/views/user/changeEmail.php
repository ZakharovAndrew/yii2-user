<?php

use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\user\Module;

$this->title = Module::t('Change Email');
$this->params['breadcrumbs'][] = $this->title;

$bootstrapVersion = Yii::$app->getModule('user')->bootstrapVersion;
$classActiveForm = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\ActiveForm";
$classHtml = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Html";
?>
<?= $this->render('_style') ?>

<div class="content">
    <div class="white-box center">
        <div class="form-box">
            <h2><?= Module::t('Change Email') ?></h2> 
            <?php $form = $classActiveForm::begin(); ?>
                <?= $form->field($model, 'password')->passwordInput()->label(Module::t('Current password')) ?>
                <?= $form->field($model, 'new_email')->textInput()->label(Module::t('New Email')) ?>
                <div class="form-group">
                    <?= Html::submitButton(Module::t('Change Email'), ['class' => 'btn btn-primary']) ?>
                </div>
            <?php $classActiveForm::end(); ?>
 
        </div>
    </div>
</div>
