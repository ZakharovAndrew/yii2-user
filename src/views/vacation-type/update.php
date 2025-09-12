<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;

use ZakharovAndrew\user\assets\UserAssets;
UserAssets::register($this);

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\user\models\VacationType */

$this->title = Module::t('Update Vacation Type: {name}', ['name' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Module::t('Vacation Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Module::t('Update');
?>
<div class="vacation-type-update">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>