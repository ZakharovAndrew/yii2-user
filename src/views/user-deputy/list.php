<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\User;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Users with Deputies');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-list-index">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                //'layout' => "{items}\n{pager}",
                'tableOptions' => ['class' => 'table table-striped table-bordered'],
                'columns' => [
                    [
                        'attribute' => 'name',
                        'label' => Module::t('User'),
                        'format' => 'raw',
                        'value' => function($model) {
                            return Html::a(
                                Html::encode($model->name),
                                ['/user/user/view', 'id' => $model->id],
                                ['data-pjax' => 0, 'target' => '_blank']
                            ) . '<br><small class="text-muted">' . Html::encode($model->username) . '</small>';
                        },
                    ],                        
                    [
                        'attribute' => 'status',
                        'label' => Module::t('Status'),
                        'filter' => User::getStatusList(),
                        'value' => function($model) {
                            $statuses = $model->getStatusList();
                            return isset($statuses[$model->status]) ? $statuses[$model->status] : $model->status;
                        },
                    ],
                    [
                        'attribute' => 'city',
                        'label' => Module::t('City'),
                        'visible' => Yii::$app->session->get('gridViewColumnVisibility')['city'] ?? false,
                    ],
                    [
                        'attribute' => 'phone',
                        'label' => Module::t('Phone'),
                        'visible' => Yii::$app->session->get('gridViewColumnVisibility')['phone'] ?? false,
                    ],
                    [
                        'label' => Module::t('Deputies'),
                        'format' => 'raw',
                        'value' => function($model) {
                            /** @var User $model */
                            $deputies = $model->getDeputies()->all();
                            
                            if (empty($deputies)) {
                                return '<span class="text-muted">' . Module::t('No deputies') . '</span>';
                            }

                            $deputiesHtml = [];
                            foreach ($deputies as $deputy) {
                                $deputyInfo = Html::a(
                                    Html::encode($deputy->deputyUser->name),
                                    ['/user/user/view', 'id' => $deputy->deputy_user_id],
                                    ['class' => 'text-nowrap', 'data-pjax' => 0, 'target' => '_blank']
                                );

                                // Добавляем информацию о периодах
                                $periodInfo = '<br><small class="text-muted">';
                                $periodInfo .= Module::t('Valid from: {date}', [
                                    'date' => Yii::$app->formatter->asDate($deputy->valid_from)
                                ]);
                                
                                if ($deputy->valid_to) {
                                    $periodInfo .= ' - ' . Yii::$app->formatter->asDate($deputy->valid_to);
                                } else {
                                    $periodInfo .= ' - ' . Module::t('No end date');
                                }
                                $periodInfo .= '</small>';

                                // Проверяем актуальность заместительства
                                $now = time();
                                $validFrom = strtotime($deputy->valid_from);
                                $validTo = $deputy->valid_to ? strtotime($deputy->valid_to) : null;

                                $isActive = $validFrom <= $now && (!$validTo || $validTo >= $now);
                                $isFuture = $validFrom > $now;
                                $isExpired = $validTo && $validTo < $now;

                                $statusBadge = '';
                                if ($isFuture) {
                                    $statusBadge = ' <span class="badge bg-warning">' . Module::t('Future') . '</span>';
                                } elseif ($isExpired) {
                                    $statusBadge = ' <span class="badge bg-danger">' . Module::t('Expired') . '</span>';
                                } else {
                                    $statusBadge = ' <span class="badge bg-success">' . Module::t('Active') . '</span>';
                                }

                                $deputiesHtml[] = '<div class="mb-2 p-2 border rounded">' . 
                                    $deputyInfo . $statusBadge . $periodInfo . 
                                    '</div>';
                            }

                            return implode('', $deputiesHtml);
                        },
                    ],
                    [
                        'label' => Module::t('Actions'),
                        'format' => 'raw',
                        'value' => function($model) {
                            return Html::a(
                                Module::t('Manage Deputies'),
                                ['/user/user-deputy/index', 'user_id' => $model->id],
                                ['class' => 'btn btn-sm btn-outline-primary', 'data-pjax' => 0]
                            );
                        },
                    ],
                ],
            ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>

</div>

<style>
.table > tbody > tr > td {
    vertical-align: middle;
}
.text-nowrap {
    white-space: nowrap;
}
.badge {
    font-size: 0.7em;
}
</style>