<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\Vacation;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\user\models\Vacation */

$this->title = Module::t('Vacation Details');
$this->params['breadcrumbs'][] = ['label' => Module::t('Vacations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Определяем доступные действия
$canProcess = $model->status == Vacation::STATUS_REQUESTED && 
              (Yii::$app->user->identity->isAdmin() || Yii::$app->user->identity->canManageUser($model->user_id));

$canEdit = $model->canBeEdited();
$canCancel = false;
//$canCancel = $model->canBeCancelled();

?>
<div class="vacation-view">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><?= Module::t('Vacation Information') ?></h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute' => 'user_id',
                                'value' => $model->user ? Html::a($model->user->name, ['/user/user/view', 'id' => $model->user_id]) : 'N/A',
                                'format' => 'raw',
                                'label' => Module::t('Employee'),
                            ],
                            [
                                'attribute' => 'type_id',
                                'value' => $model->type ? $model->type->name : 'N/A',
                                'label' => Module::t('Vacation Type'),
                            ],
                            [
                                'attribute' => 'start_date',
                                'format' => ['date', 'php:d.m.Y'],
                            ],
                            [
                                'attribute' => 'end_date',
                                'format' => ['date', 'php:d.m.Y'],
                            ],
                            [
                                'attribute' => 'days_count',
                                'value' => function($model) {
                                    return $model->days_count . ' ' . Module::t('days');
                                },
                            ],
                            [
                                'attribute' => 'status',
                                'value' => function($model) {
                                    $status = $model->getStatusLabel();
                                    return Html::tag('span', $status['label'], [
                                        'class' => 'badge ' . $status['class']
                                    ]);
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'comment',
                                'value' => $model->comment ? nl2br(Html::encode($model->comment)) : Module::t('No comments'),
                                'format' => 'raw',
                            ],
                        ],
                    ]) ?>
                </div>
            </div>

            <?php if ($model->approved_by || $model->rejected_by): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title"><?= Module::t('Processing Information') ?></h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => array_filter([
                            $model->approved_by ? [
                                'attribute' => 'approved_by',
                                'value' => $model->approvedBy ? $model->approvedBy->name : 'N/A',
                                'label' => Module::t('Approved By'),
                            ] : null,
                            $model->approved_at ? [
                                'attribute' => 'approved_at',
                                'format' => ['datetime', 'php:d.m.Y H:i'],
                                'label' => Module::t('Approval Date'),
                            ] : null,
                            $model->rejected_by ? [
                                'attribute' => 'rejected_by',
                                'value' => $model->rejectedBy ? $model->rejectedBy->name : 'N/A',
                                'label' => Module::t('Rejected By'),
                            ] : null,
                            $model->rejected_at ? [
                                'attribute' => 'rejected_at',
                                'format' => ['datetime', 'php:d.m.Y H:i'],
                                'label' => Module::t('Rejection Date'),
                            ] : null,
                        ]),
                    ]) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><?= Module::t('Actions') ?></h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($canProcess): ?>
                            <?= Html::a(Module::t('Process Request'), ['process-request', 'id' => $model->id], [
                                'class' => 'btn btn-warning btn-lg'
                            ]) ?>
                        <?php endif; ?>

                        <?php if ($canEdit): ?>
                            <?= Html::a(Module::t('Edit'), ['update', 'id' => $model->id], [
                                'class' => 'btn btn-primary'
                            ]) ?>
                        <?php endif; ?>

                        <?php if ($canCancel): ?>
                            <?= Html::a(Module::t('Cancel Vacation'), ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => Module::t('Are you sure you want to cancel this vacation?'),
                                    'method' => 'post',
                                ],
                            ]) ?>
                        <?php endif; ?>

                        <?= Html::a(Module::t('Back to List'), ['index'], [
                            'class' => 'btn btn-secondary'
                        ]) ?>

                        <?= Html::a(Module::t('Print'), ['print', 'id' => $model->id], [
                            'class' => 'btn btn-outline-info',
                            'target' => '_blank'
                        ]) ?>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title"><?= Module::t('Vacation Status') ?></h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <?php
                        $status = $model->getStatusLabel();
                        $icon = '';
                        $text = '';
                        
                        switch ($model->status) {
                            case Vacation::STATUS_REQUESTED:
                                $icon = '⏳';
                                $text = Module::t('Waiting for approval');
                                break;
                            case Vacation::STATUS_APPROVED:
                                if ($model->isActive()) {
                                    $icon = '🏖️';
                                    $text = Module::t('Currently on vacation');
                                } elseif ($model->isUpcoming()) {
                                    $icon = '📅';
                                    $text = Module::t('Upcoming vacation');
                                } else {
                                    $icon = '✅';
                                    $text = Module::t('Approved');
                                }
                                break;
                            case Vacation::STATUS_REJECTED:
                                $icon = '❌';
                                $text = Module::t('Vacation rejected');
                                break;
                            case Vacation::STATUS_CANCELLED:
                                $icon = '🚫';
                                $text = Module::t('Vacation cancelled');
                                break;
                            case Vacation::STATUS_COMPLETED:
                                $icon = '✔️';
                                $text = Module::t('Vacation completed');
                                break;
                        }
                        ?>
                        
                        <div style="font-size: 3rem; margin-bottom: 15px;"><?= $icon ?></div>
                        <h4><?= $text ?></h4>
                        
                        <?php if ($model->isUpcoming()): ?>
                            <p class="text-muted">
                                <?= Module::t('Starts in {days} days', [
                                    'days' => $model->getDaysUntilStart()
                                ]) ?>
                            </p>
                        <?php elseif ($model->isActive()): ?>
                            <p class="text-success">
                                <strong><?= Module::t('Currently active') ?></strong>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($model->status == Vacation::STATUS_APPROVED): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title"><?= Module::t('Vacation Timeline') ?></h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item <?= $model->created_at ? 'active' : '' ?>">
                            <div class="timeline-point"></div>
                            <div class="timeline-content">
                                <small><?= Module::t('Requested') ?></small>
                                <br>
                                <small class="text-muted">
                                    <?= $model->created_at ? Yii::$app->formatter->asDatetime($model->created_at) : '' ?>
                                </small>
                            </div>
                        </div>
                        
                        <?php if ($model->approved_at): ?>
                        <div class="timeline-item active">
                            <div class="timeline-point success"></div>
                            <div class="timeline-content">
                                <small><?= Module::t('Approved') ?></small>
                                <br>
                                <small class="text-muted">
                                    <?= Yii::$app->formatter->asDatetime($model->approved_at) ?>
                                </small>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($model->start_date): ?>
                        <div class="timeline-item <?= time() >= strtotime($model->start_date) ? 'active' : '' ?>">
                            <div class="timeline-point primary"></div>
                            <div class="timeline-content">
                                <small><?= Module::t('Starts') ?></small>
                                <br>
                                <small class="text-muted">
                                    <?= Yii::$app->formatter->asDate($model->start_date) ?>
                                </small>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($model->end_date): ?>
                        <div class="timeline-item <?= time() >= strtotime($model->end_date) ? 'active' : '' ?>">
                            <div class="timeline-point info"></div>
                            <div class="timeline-content">
                                <small><?= Module::t('Ends') ?></small>
                                <br>
                                <small class="text-muted">
                                    <?= Yii::$app->formatter->asDate($model->end_date) ?>
                                </small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php
// CSS стили для timeline
$this->registerCss(<<<CSS
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-point {
    position: absolute;
    left: -20px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #dee2e6;
    border: 2px solid #fff;
}

.timeline-point.success {
    background-color: #28a745;
}

.timeline-point.primary {
    background-color: #007bff;
}

.timeline-point.info {
    background-color: #17a2b8;
}

.timeline-item.active .timeline-point {
    background-color: #007bff;
}

.timeline-content {
    margin-left: 10px;
}

.timeline-item:not(:last-child):after {
    content: '';
    position: absolute;
    left: -15px;
    top: 17px;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-item.active:after {
    background-color: #007bff;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.1rem;
}

.d-grid.gap-2 .btn {
    margin-bottom: 0.5rem;
}
CSS
);
?>