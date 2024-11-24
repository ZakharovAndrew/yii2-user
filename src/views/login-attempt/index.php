<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Login Attempts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="login-attempt-index">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

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