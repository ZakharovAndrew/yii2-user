<?php

use yii\helpers\Html;
use ZakharovAndrew\user\assets\UserAssets;
use yii\helpers\Url;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\User $model */

$this->title = 'Dashboard';
?>

<div class="user-profile">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <div class="white-block">
        <table class="table table-bordered">
            <?php foreach ($data as $row) { var_dump($row);?>
            <tr><td><?= $row['cnt'] ?></td><td><?= $row['values'] ?></td></tr>
            <?php } ?>
        </table>
    </div>
</div>