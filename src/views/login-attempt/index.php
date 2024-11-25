<?php

use yii\grid\GridView;
use yii\helpers\Html;
use ZakharovAndrew\user\assets\UserAssets;

UserAssets::register($this);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Login Attempts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="login-attempt-index">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <div class="white-block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'columns' => [
            'id',
            'username',
            'ip_address',
            'attempt_time',
            [
                'attribute' => 'successful',
                'format' => 'boolean',
                'label' => 'Successful Attempt',
            ],
        ],
    ]); ?>
    </div>
</div>