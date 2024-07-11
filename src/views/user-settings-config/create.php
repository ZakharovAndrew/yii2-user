<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\UserSettingsConfig $model */

$this->title = Module::t('Create User Settings Configuration');
$this->params['breadcrumbs'][] = ['label' => Module::t('User Settings Configurations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-settings-config-create">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
