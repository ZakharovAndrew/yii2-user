<?php

use ZakharovAndrew\user\models\UserSettingsConfig;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use ZakharovAndrew\user\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\UserSettingsConfigSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('User Settings Configurations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-settings-config-index">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <p>
        <?= Html::a(Module::t('Create User Settings Configuration'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'title',
            'code',
            [
                'attribute' => 'type',
                'format' => 'raw',
                'filter' => UserSettingsConfig::getTypeOfSettings(),
                'value' => function ($model) {
                    
                    return UserSettingsConfig::getTypeOfSettings()[$model->type] ?? '';
                },
            ],
            [
                'attribute' => 'access_level',
                'format' => 'raw',
                'filter' => UserSettingsConfig::getAccessLevel(),
                'value' => function ($model) {
                    
                    return UserSettingsConfig::getAccessLevel()[$model->access_level] ?? '';
                },
            ],
            //'values:ntext',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, UserSettingsConfig $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
