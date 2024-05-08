<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;

/** @var yii\web\View $this */
/** @var app\models\Roles $model */

$this->title = Module::t('Create Roles');
$this->params['breadcrumbs'][] = ['label' => Module::t('Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="roles-create">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
