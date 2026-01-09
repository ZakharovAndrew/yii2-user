<?php
 
use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\user\Module;
 
$this->title = Module::t('Reset password');
$bootstrapVersion = Yii::$app->getModule('user')->bootstrapVersion;
$classActiveForm = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\ActiveForm";
$classHtml = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Html";

$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_style') ?>

<div class="content">
    <div class="white-box center">
        <div class="form-box">
            <h2><?= Module::t('Please enter a new password') ?></h2> 
            <?php $form = $classActiveForm::begin(['id' => 'reset-password-form', 'action' => Url::to([$reset_password_link ?? '/user/user/reset-password', 'token' => $token]), 'method' => 'POST']); ?>
                <?= $form->field($model, 'password')->passwordInput(['autofocus' => true])->label('Пароль') ?>
                <div class="form-group">
                    <?= $classHtml::submitButton(Module::t('Save'), ['class' => 'btn btn-primary']) ?>
                </div>
            <?php $classActiveForm::end(); ?>
 
        </div>
    </div>
</div>