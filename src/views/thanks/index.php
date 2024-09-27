<?php

use ZakharovAndrew\user\models\Roles;
use ZakharovAndrew\user\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use ZakharovAndrew\user\assets\UserAssets;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var app\models\ThanksSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Appreciation');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="roles-index">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <p>
        <?= Html::a(Module::t('Send thanks'), ['send'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    $items = $dataProvider->getModels();
    foreach ($items as $thanks) { ?>
        <div class="white-block">
            <?= $thanks->text ?>
        </div>
    <?php } ?>

</div>
