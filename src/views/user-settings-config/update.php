<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\UserSettingsConfig $model */

$this->title = 'Update User Settings Config: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'User Settings Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-settings-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
