<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use ZakharovAndrew\user\Module;

$this->title = Module::t('Sign in');
$bootstrapVersion = Yii::$app->getModule('user')->bootstrapVersion;
$classActiveForm = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\ActiveForm";

$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .form-login a {
        color: #7366ff;text-align: right;
    }
    h1 {text-align: center;}
    .form-login {
        width: 450px;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 0 37px rgba(8, 21, 66, 0.05);
        margin: 0 auto;
        background-color: #fff;
    }
    .form-login-header {
        background-color: #03a9f4;
        color: #fff;
        padding: 13px;
        font-size: 18px;
        font-family: Verdana;
        font-weight: bold;
        margin-bottom: 22px;
        border-top-right-radius: 3px;
        border-top-left-radius: 3px;
    }
    
    .site-login .form-control {
        background-color: #f3f3ff;
        transition: all 0.3s ease;
        border-color: #efefef;
    font-size: 14px;
    color: #898989;
    padding: 12px 10px;
    -webkit-box-shadow: none;
    box-shadow: none;
    }
    body {
        background: #f8f9fe;
        background-size: cover;
    }
    h1 {
        margin-bottom: 30px;
    }
    .field-loginform-rememberme {
        float:left;
    }
    .content {
        justify-content: center;
        align-items: center;
        display: flex;
        padding:11%;
    }
    .btn-primary {
        text-transform: uppercase;
        background-color: #7366ff;
        border-color: #7366ff;
    }
    .btn-primary:hover {
        background-color: #7366ff;
        border-color: #7366ff;
    }
    .form-login .col-form-label {
        font-size: 16px !important;
        letter-spacing: 0.4px;

        padding-top: 1px !important;
        padding-bottom: 4px !important;

        padding-top: calc(0.375rem + 1px);
        padding-bottom: calc(0.375rem + 1px);
        margin-bottom: 0;
        font-size: inherit;
        line-height: 1.5;
    }
    .field-loginform-rememberme {margin-top:10px;margin-bottom:45px!important;}
    .mg10 {padding-top:10px}
    p {
        margin-bottom: 25px;
        font-size: 14px;
        color: #898989;
    }
    h4 {
        margin-bottom: 5px;
    }
</style>
<div class="content">
    <div class="site-login center">
        <div class="form-login">
            <h1><?= $this->title ?></h1>

            <?php $form = $classActiveForm::begin([
                'id' => 'login-form',
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "<label class=\"col-form-label\">{label}</label><div class=\"col-lg-12\">{input}</div>\n<br><div class=\"\">{error}</div>",
                    //'labelOptions' => ['class' => ''], //col-lg-1 control-label
                ],
            ]); ?>

            <?= $form->field($model, 'username', [
                'inputOptions' => [
                'placeholder' => $model->getAttributeLabel(Module::t('Username')),
            ]])->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'password', [
            'inputOptions' => [
                'placeholder' => $model->getAttributeLabel('*******'),
            ]])->passwordInput() ?>

            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => "<div class=\"pull-left col-md-12\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
            ])->label(Module::t('Remember me')) ?>
            
            <div class="mb-3 row mg10">
                <?= Html::a(Module::t('Forgot password?'), ['site/request-password-reset']) ?>
            </div>

            <div class="form-group">
                <div class="col-lg-12">
                    <?= Html::submitButton(Module::t('Sign in'), ['class' => 'btn btn-primary col-md-12', 'name' => 'login-button']) ?>
                </div>
            </div>


            <?php $classActiveForm::end(); ?>
        </div>
    </div>
</div>
