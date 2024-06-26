<?php
 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use ZakharovAndrew\user\Module;

$bootstrapVersion = Yii::$app->getModule('user')->bootstrapVersion;
$classActiveForm = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\ActiveForm";
$classHtml = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Html";

$this->title = Module::t('Reset password');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $form = $classActiveForm::begin([]);?>
    <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <div class="form-group">
        <div class="col-lg-12">
            <?= $classHtml::submitButton('Отправить', ['class' => 'login-button']) ?>
        </div>
    </div>
<?php $classActiveForm::end(); ?>
