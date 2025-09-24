<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use ZakharovAndrew\user\models\Wallpaper;
use ZakharovAndrew\user\Module;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('Wallpapers');
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="wallpaper-admin-index">
    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <p>
        <?= Html::a(Module::t('Create Wallpaper'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'image_url',
                        'format' => 'html',
                        'value' => function($model) {
                            return $model->image_url ? Html::img($model->image_url, [
                                'style' => 'max-width: 100px; max-height: 60px; object-fit: cover;'
                            ]) : '-';
                        },
                        'contentOptions' => ['style' => 'width: 120px;']
                    ],
                    'name',


                    [
                        'attribute' => 'status',
                        'value' => function($model) {
                            return $model->getStatusList()[$model->status];
                        },
                        'filter' => Wallpaper::getStatusList(),
                        'contentOptions' => ['style' => 'width: 100px;']
                    ],
                    [
                        'attribute' => 'roles',
                        'value' => function($model) {
                            if (empty($model->roles)) {
                                return Module::t('All roles');
                            }
                            return implode(', ', $model->getAllowedRolesArray());
                        },
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'contentOptions' => ['style' => 'width: 150px;']
                    ],
                                [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{up} {down} {toggle-status} {update} {delete}',
                        'buttons' => [
                            'up' => function($url, $model, $key) {
                                if ($model->position > 1) {
                                    return Html::a(
                                        '<svg width="12" height="12" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 4L4 8M8 4L12 8M8 4V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                                        ['move-up', 'id' => $model->id],
                                        [
                                            'title' => Module::t('Move up'),
                                            'class' => 'btn btn-xs btn-default',
                                            'data' => [
                                                'method' => 'post',
                                                'pjax' => 1,
                                            ],
                                        ]
                                    );
                                }
                                return '';
                            },
                            'down' => function($url, $model, $key) {
                                $maxPosition = Wallpaper::find()->max('position');
                                if ($model->position < $maxPosition) {
                                    return Html::a(
                                        '<svg width="12" height="12" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 12L4 8M8 12L12 8M8 12V4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                                        ['move-down', 'id' => $model->id],
                                        [
                                            'title' => Module::t('Move down'),
                                            'class' => 'btn btn-xs btn-default',
                                            'data' => [
                                                'method' => 'post',
                                                'pjax' => 1,
                                            ],
                                        ]
                                    );
                                }
                                return '';
                            },
                            'toggle-status' => function($url, $model, $key) {
                                $icon = $model->status == Wallpaper::STATUS_ACTIVE ? 'fa-ban' : 'fa-check';
                                $title = $model->status == Wallpaper::STATUS_ACTIVE ? 
                                    Module::t('Deactivate') : Module::t('Activate');
                                return Html::a('<i class="fas ' . $icon . '"></i>', 
                                    ['toggle-status', 'id' => $model->id], 
                                    [
                                        'title' => $title,
                                        'data' => [
                                            'method' => 'post',
                                            'confirm' => Module::t('Are you sure you want to change status?')
                                        ]
                                    ]
                                );
                            }
                        ],

                        'contentOptions' => ['style' => 'width: 120px; text-align: center;']
                    ],
                ],
            ]); ?>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
<style>
   .btn-xs {
    padding: 4px 5px;
    font-size: 12px;
    line-height: 1;
    border-radius: 3px;
    margin: 0 2px;
    transition: all 0.2s ease;
}

.btn-xs:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}

.btn-default {
    background: #f8f9fa;
    border-color: #ddd;
}

.btn-default:hover {
    background: #e9ecef;
    border-color: #ccc;
}
</style>
