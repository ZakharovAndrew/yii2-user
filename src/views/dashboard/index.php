<?php

use yii\helpers\Html;
use ZakharovAndrew\user\assets\UserAssets;
use yii\helpers\Url;
use ZakharovAndrew\user\Module;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\User $model */

$this->title = 'Dashboard';
?>

<div class="user-profile">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <div class="white-block">
        <form method="get">
            <div class="form-group" style="display:flex">
                <?= Html::dropDownList('setting', $setting, $settings, ['class' => 'form-control form-select'])?>
                <input type="submit" value="<?= Module::t('Show') ?>" class="btn btn-success" style="margin-left: 10px">
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><?= Module::t('Value') ?></th>
                    <th><?= Module::t('Number of users') ?></th>
                </tr>
            </thead>
            <?php foreach ($data as $row) {?>
            <tr><td><?= $row['setting_value'] == 'Value not set' ? Module::t('Value not set') :  $row['setting_value'] ?></td><td><?= $row['cnt'] ?></td></tr>
            <?php } ?>
        </table>
    </div>
</div>