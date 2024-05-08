<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = Module::t('Create User');
$this->params['breadcrumbs'][] = ['label' => Module::t('Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
