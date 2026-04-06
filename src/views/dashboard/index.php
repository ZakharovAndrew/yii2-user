<?php

use yii\helpers\Html;
use ZakharovAndrew\user\assets\UserAssets;
use yii\helpers\Url;
use ZakharovAndrew\user\Module;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\User $model */

$this->title = 'Dashboard';

 // CSS/JS Select2
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
// init Select2
$this->registerJs(<<<JS
    $('.select2').select2({
        placeholder: "",
        allowClear: true,
        width: '100%',
        theme: 'bootstrap'
    });
JS
);
?>
<style>
    .table-bordered td {
        padding: 2px 8px !important;
    }
    .table-bordered tr:hover td {
        background-color:#e0f7fa;
    }
    .user-dashboard .select2-container--default .select2-selection--single,
    .user-dashboard .select2-container .select2-selection--multiple
    {
        background: #f5f8fa;
        border: none;
    }
</style>

<div class="user-dashboard">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <div class="white-block">
        <form method="get">
            <div class="form-group" style="display:flex; gap:10px">
                <?= Html::dropDownList('setting', $setting, $settings, ['class' => 'form-control form-select'])?>
                <?= Html::dropDownList('setting2', $setting2, $settings, ['class' => 'form-control form-select', 'prompt' => ''])?>
                <?= Html::dropDownList('status', $status ?? null, Yii::$app->user->identity::getStatusList(), ['class' => 'form-control form-select select2 ', 'prompt' => '', 'multiple' => true])?>
                <input type="submit" value="<?= Module::t('Show') ?>" class="btn btn-success" style="margin-left: 10px">
            </div>
        </form>
        <div class="table-responsive">
            <?php if (!$setting2) { ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th><?= Module::t('Value') ?></th>
                        <th><?= Module::t('Number of users') ?></th>
                    </tr>
                </thead>
                <?php foreach ($data as $row) {?>
                <tr><td><?= $row['setting_value'] == 'Value not set' ? Module::t('Value not set') : $row['setting_value'] ?></td><td><?= $row['cnt'] ?></td></tr>
                <?php } ?>
            </table>
            <?php } else { ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th><?= Module::t('Value') ?></th>
                        <?php foreach ($setting2_column as $column2) {?>
                        <th><?= $column2 ?></th>
                        <?php }?>
                    </tr>
                </thead>
                <?php foreach ($setting_column as $column) {?>
                    <tr>
                        <td><?= $column == 'Value not set'  ? Module::t('Value not set') : $column ?></td>
                        <?php foreach ($setting2_column as $column2) {?>
                        <td><?= $data[$column][$column2] ?? '' ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </table>
            <?php } ?>
        </div>
    </div>
</div>