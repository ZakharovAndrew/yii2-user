<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\Vacation;

/* @var $this yii\web\View */
/* @var $searchModel ZakharovAndrew\user\models\VacationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('Vacations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vacation-index">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <p>
        <?= Html::a(Module::t('Request Vacation'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Module::t('My Vacations'), ['my'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Module::t('Calendar View'), ['calendar'], ['class' => 'btn btn-info']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'user_name',
                'label' => Module::t('Employee'),
                'value' => function($model) {
                    return $model->user ? $model->user->name : 'N/A';
                },
            ],
            [
                'attribute' => 'type_id',
                'label' => Module::t('Vacation Type'),
                'value' => function($model) {
                    return $model->type ? $model->type->name : 'N/A';
                },
                'filter' => \yii\helpers\ArrayHelper::map(
                    \ZakharovAndrew\user\models\VacationType::find()->all(), 
                    'id', 
                    'name'
                ),
            ],
            [
                'attribute' => 'start_date',
                'format' => ['date', 'php:d.m.Y'],
                /*'filter' => \yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'start_date',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class' => 'form-control'],
                ]),*/
            ],
            [
                'attribute' => 'end_date',
                'format' => ['date', 'php:d.m.Y'],
                /*'filter' => \yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'end_date',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class' => 'form-control'],
                ]),*/
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
                'template' => '{view} {update} {approve} {reject} {delete}',
                'buttons' => [
                    'approve' => function ($url, $model, $key) {
                        if ($model->status != Vacation::STATUS_REQUESTED || !Yii::$app->user->can('approveVacation')) {
                            return '';
                        }
                        return Html::a(
                            '<span class="glyphicon glyphicon-ok"></span>',
                            $url,
                            [
                                'title' => Module::t('Approve'),
                                'data' => [
                                    'method' => 'post',
                                    'confirm' => Module::t('Are you sure you want to approve this vacation?'),
                                ],
                            ]
                        );
                    },
                    'reject' => function ($url, $model, $key) {
                        if ($model->status != Vacation::STATUS_REQUESTED || !Yii::$app->user->can('approveVacation')) {
                            return '';
                        }
                        return Html::a(
                            '<span class="glyphicon glyphicon-remove"></span>',
                            $url,
                            [
                                'title' => Module::t('Reject'),
                                'data' => [
                                    'method' => 'post',
                                    'confirm' => Module::t('Are you sure you want to reject this vacation?'),
                                ],
                            ]
                        );
                    },
                    'delete' => function ($url, $model, $key) {
                        /*if (!$model->canBeCancelled()) {
                            return '';
                        }*/
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
                    },
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>