<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\Vacation;

/* @var $this yii\web\View */
/* @var $searchModel ZakharovAndrew\user\models\VacationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('My Vacations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vacation-my">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <p>
        <?= Html::a(Module::t('Request Vacation'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Module::t('All Vacations'), ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'type_id',
                'label' => Module::t('Vacation Type'),
                'value' => function($model) {
                    return $model->type ? $model->type->name : 'N/A';
                },
            ],
            [
                'attribute' => 'start_date',
                'format' => ['date', 'php:d.m.Y'],
            ],
            [
                'attribute' => 'end_date',
                'format' => ['date', 'php:d.m.Y'],
            ],
            'days_count',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    $status = $model->getStatusLabel();
                    return Html::tag('span', $status['label'], ['class' => $status['class']]);
                },
                'format' => 'raw',
                'filter' => Vacation::getStatusList(),
            ],
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:d.m.Y H:i'],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            $url,
                            ['title' => Module::t('View')]
                        );
                    },
                    'update' => function ($url, $model, $key) {
                        if (!$model->canBeEdited()) {
                            return '';
                        }
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
                            $url,
                            ['title' => Module::t('Update')]
                        );
                    },
                    /*'delete' => function ($url, $model, $key) {
                        if (!$model->canBeCancelled()) {
                            return '';
                        }
                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"></span>',
                            $url,
                            [
                                'title' => Module::t('Delete'),
                                'data' => [
                                    'method' => 'post',
                                    'confirm' => Module::t('Are you sure you want to delete this vacation?'),
                                ],
                            ]
                        );
                    },*/
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>