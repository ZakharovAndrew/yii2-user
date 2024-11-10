<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\models\Roles;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\assets\UserAssets;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\UserRoles $model */
/** @var yii\widgets\ActiveForm $form */

$list = [];
$roles = Roles::find()->all();
foreach ($roles as $role) {
    $list[$role->id] = $role->getSubjects();
}
$listJson = json_encode($list);
$script = <<< JS
let rolesList = $listJson;
const roleSubject = $("#role_subject");

function setSubjects() {
    let role_id = $("#userroles-role_id").val();
    let list = rolesList[role_id];
    if (typeof list === 'object' && list !== null && list.length !== 0) {
        let html_select = '<option></option>';
        Object.entries(list).forEach(([key, value]) => {
            html_select += `<option value="\${key}">\${value}</option>`;
        });
        roleSubject.html(html_select);
        $("#userroles-subject_id").parent().hide();
        $("#role_subject_group").show();
    } else {
        $("#role_subject_group").hide();
        $("#userroles-subject_id").val('');
        $("#userroles-subject_id").parent().show();
    }
}
$("#userroles-role_id").on('change', setSubjects);

roleSubject.on('change', function(){
    $("#userroles-subject_id").val($("#role_subject").val());
});
        
// init
setSubjects();
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>

<div class="user-roles-form white-block">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>
    
    <?= $form->field($model, 'role_id')->dropDownList(Roles::getRolesList()) ?>
    
    <?= $form->field($model, 'subject_id')->textInput(['maxlength' => true]) ?>
    
    <div id="role_subject_group" class="form-group" style="display: none">
        <label class="control-label" for="role_subject"><?= Module::t('Subject of the role') ?></label>
        <select id="role_subject" class="form-control form-select"></select>
    </div>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
