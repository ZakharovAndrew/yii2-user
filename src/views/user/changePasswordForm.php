<?php

use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\user\Module;

$this->title = Module::t('Change Password');
$this->params['breadcrumbs'][] = $this->title;
$bootstrapVersion = Yii::$app->getModule('user')->bootstrapVersion;
$classActiveForm = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\ActiveForm";
?>
<?= $this->render('_style') ?>

<div class="content">
    <div class="white-box center">
        <div class="form-box">
            <h2><?= $this->title ?></h2> 
            <?php $form = $classActiveForm::begin(['id' => 'user-change-password', 'action' => Url::to(['/user/user/change-password'])]); ?>
                <?= $form->field($model, 'old_password')->passwordInput() ?>
                <?= $form->field($model, 'new_password')->passwordInput() ?>
                <?= $form->field($model, 'new_password_repeat')->passwordInput() ?>
                <div class="form-group">
                    <?= Html::submitButton(Module::t('Change Password'), ['class' => 'btn btn-primary']) ?>
                </div>
            <?php $classActiveForm::end(); ?>
        </div>
    </div>
</div>