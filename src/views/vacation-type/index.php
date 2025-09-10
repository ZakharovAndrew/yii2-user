<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use ZakharovAndrew\user\Module;

/* @var $this yii\web\View */
/* @var $searchModel ZakharovAndrew\user\models\VacationTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('Vacation Types');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vacation-type-index">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <p>
        <?= Html::a(Module::t('Create Vacation Type'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            [
                'attribute' => 'is_paid',
                'format' => 'boolean',
                'filter' => [1 => Module::t('Yes'), 0 => Module::t('No')],
            ],
            [
                'attribute' => 'is_active',
                'format' => 'boolean',
                'filter' => [1 => Module::t('Yes'), 0 => Module::t('No')],
            ],
            'max_days_per_year',
            'priority',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {toggle-active} {delete}',
                'buttons' => [
                    'toggle-active' => function ($url, $model, $key) {
                        return Html::a(
                            $model->is_active ? '
<svg xmlns="http://www.w3.org/2000/svg" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1em" viewBox="0 0 1200 1200" xml:space="preserve"><path fill="currentColor" d="M1024.263 175.738c-234.317-234.317-614.192-234.317-848.525 0-234.317 234.317-234.317 614.192 0 848.525 234.317 234.316 614.192 234.316 848.525 0 234.316-234.318 234.316-614.193 0-848.525zm-163.489 57.44L233.193 860.743c-125.257-175.737-109.044-421.274 48.624-578.942s403.219-173.881 578.942-48.624h.015zm106.064 106.048c125.248 175.738 109.031 421.29-48.654 578.942-157.652 157.683-403.205 173.911-578.942 48.639l627.581-627.581h.015z"/></svg>' : '
<svg xmlns="http://www.w3.org/2000/svg" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1em" viewBox="0 0 24 24" xml:space="preserve"><path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.5 0-8-3.5-8-8s3.5-8 8-8 8 3.5 8 8-3.5 8-8 8z"/><path d="m9.8 16.8-3.7-3.6 1.4-1.5L9.8 14l5.7-6.1L17 9.3z"/><path style="fill:none" color="currentColor"  d="M0 0h24v24H0z"/></svg>',
                            ['toggle-active', 'id' => $model->id],
                            [
                                'title' => $model->is_active ? Module::t('Deactivate') : Module::t('Activate'),
                                'data-pjax' => '0',
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>