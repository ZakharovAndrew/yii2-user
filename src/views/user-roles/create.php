<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UserRoles $model */

$this->title = 'Create User Roles';
$this->params['breadcrumbs'][] = ['label' => 'User Roles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-roles-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
