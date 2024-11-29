<?php
use ZakharovAndrew\user\models\Roles;


/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\UserRoles $model */
/** @var yii\widgets\ActiveForm $form */

$rolesWithSubjectsJson = json_encode(Roles::getRolesWithSubjects());

$script = <<< JS
let rolesList = $rolesWithSubjectsJson;
const roleSubject = $("#role_subject");
let role_id = $("#role_id");
let subject_id = $("#subject_id");

function setSubjects() {
    let list = rolesList[role_id.val()];
    if (typeof list === 'object' && list !== null && list.length !== 0) {
        let html_select = '<option></option>';
        Object.entries(list).forEach(([key, value]) => {
            html_select += `<option value="\${key}">\${value}</option>`;
        });
        roleSubject.html(html_select);
        subject_id.parent().hide();
        $("#role_subject_group").show();
    } else {
        $("#role_subject_group").hide();
        subject_id.val('');
        subject_id.parent().show();
    }
}
role_id.on('change', setSubjects);

roleSubject.on('change', function(){
    subject_id.val($("#role_subject").val());
});
        
// init
setSubjects();
JS;

$this->registerJs($script, yii\web\View::POS_READY);
