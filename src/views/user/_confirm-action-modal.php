<?php 

use ZakharovAndrew\user\Module;
use yii\helpers\Html;

$bootstrapVersion = Yii::$app->getModule('user')->bootstrapVersion;
$classModal = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Modal";
$modalTitle = ($bootstrapVersion == 3 ? 'header' : 'title');
?>


<?php $classModal::begin([
    'id' => $id,
    $modalTitle => '<h2>'.Module::t($title).'</h2>',
    'footer' => ''             
]) ?>

    <p style="text-align:center">
        <?= Module::t('Are you sure you want to reset the passwords for the selected users?') ?>
    </p>
    <div style="display:flex;justify-content: center;gap:4px;">
        <?= Html::submitButton(Module::t($action), ['name' => 'form-action', 'value' => $action, 'class' => 'btn btn-danger']) ?>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal"><?= Module::t('Cancel') ?></button>
    </div>
    
<?php $classModal::end() ?>