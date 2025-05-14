<?php

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\user\models\LoginForm */

use ZakharovAndrew\user\Module;

$this->title = Module::t('Registration is disabled');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    h1 {text-align: center;margin-bottom: 30px;}
    body {
        background: #f8f9fe;
        background-size: cover;
    }
</style>
<div class="content">
    <div class="site-login center">
        <div class="form-login">
            <h1><?= $this->title ?></h1>
        </div>
    </div>
</div>
