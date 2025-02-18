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
/** @var ZakharovAndrew\user\models\RolesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Roles');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .white-block .table-bordered {
        box-shadow: none;
    }
</style>
<div class="roles-index">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <p>
        <?= Html::a(Module::t('Create Roles'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="white-block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //'id',
            'title',
            'code',
            'description:ntext',
            'created_at',
            [
                'class' => ActionColumn::className(),
                'template' => '{update}',
                'urlCreator' => function ($action, Roles $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>
    </div>


</div>
