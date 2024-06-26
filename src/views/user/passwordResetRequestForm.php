<?php
 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use ZakharovAndrew\user\Module;
 
$this->title = Module::t('Reset password');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $form = ActiveForm::begin([]);?>
    <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <div class="form-group">
        <div class="col-lg-12">
            <?= Html::submitButton('Отправить', ['class' => 'login-button']) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>
