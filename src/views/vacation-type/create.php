<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;

use ZakharovAndrew\user\assets\UserAssets;
UserAssets::register($this);

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\user\models\VacationType */

$this->title = Module::t('Create Vacation Type');
$this->params['breadcrumbs'][] = ['label' => Module::t('Vacation Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vacation-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>