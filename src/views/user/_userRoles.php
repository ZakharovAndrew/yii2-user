<?php

use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\UserRoles;
use ZakharovAndrew\user\models\Roles;
use yii\helpers\Html;
use yii\helpers\Url;

// Get all user roles
$roles = UserRoles::getUserRoles($model->id);
                  
foreach ($roles as $role) {
    $item = Roles::findOne($role['role_id']);
    echo $role['title'] . (isset($role['subject_id']) ? '('.$item->roleSubject($role['subject_id']).')' : '' );
    echo ' <a href="'.Url::toRoute(['/user/user-roles/delete', 'id' => $role['id']]).'" title="'.Module::t('Delete role').'" aria-label="'.Module::t('Delete role').'" class="role-delete">X</a>';
}

echo Html::a('+', ['/user/user-roles/create', 'user_id' => $model->id, 'form'=>'_form_good_product'], ['title' => Module::t('Add Role'), 'aria-label' => Module::t('Add Role'), 'class' => "btn btn-sm btn-success"]);
