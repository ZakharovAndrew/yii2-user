<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\UserRoles $model */

$this->title = Module::t('Add a role to a user');
$this->params['breadcrumbs'][] = ['label' => Module::t('Users'), 'url' => ['user/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-roles-create">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
