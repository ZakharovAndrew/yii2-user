<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use ZakharovAndrew\user\models\Wallpaper;
use use ZakharovAndrew\user\Module;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('Wallpapers Management');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wallpaper-admin-index">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h1><?= Html::encode($this->title) ?></h1>
                    <div class="float-right">
                        <?= Html::a(Module::t('Create Wallpaper'), ['create'], ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
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
                                'attribute' => 'position',
                                'contentOptions' => ['style' => 'width: 80px; text-align: center;']
                            ],
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
                                'template' => '{view} {update} {toggle-status} {delete}',
                                'buttons' => [
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
    </div>
</div>
