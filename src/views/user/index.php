<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\UserRoles;
use ZakharovAndrew\user\models\Roles;

use ZakharovAndrew\user\assets\UserAssets;
UserAssets::register($this);

$bootstrapVersion = Yii::$app->getModule('user')->bootstrapVersion;
$classModal = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Modal";
$classButtonDropdown = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\ButtonDropdown";

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Users');
$this->params['breadcrumbs'][] = $this->title;

// Получаем настройки пользователя из сессии
$columnVisibility = \ZakharovAndrew\user\models\User::getColumnVisibility();

$toggleUrl = Url::to(['/user/user/toggle-column-visibility']);
$language = \Yii::$app->language;
$waitMessage = Module::t('Processing, please wait..');

$script = <<< JS
$('#submit-reset-password').on('click', function() {
    $('#reset-password-modal .modal-body').text('$waitMessage');
});
        
$('.toggleColumn').on('click', function() {
    let column = $(this).data('column');
    let visibility = $(this).data('visibility');
    console.log("$toggleUrl?column=" + column);
    $.ajax({
        type: "POST",
        url: "$toggleUrl",
        data: {
            column: column,
            visibility: visibility
        },
        success: function(data) {
            console.log('asd');
            $('#users').yiiGridView('applyFilter');
        }
    });
});

    $("#settings").click(function() {
        if ($(".settings-modal").hasClass('show')) {
            $(".settings-modal").removeClass('show');
        } else {
            $(".settings-modal").addClass('show');
        }
    });

    $(".settings-modal .btn-modal-close").click(function() {
        $(this).parent().parent().removeClass('show');
    });
        
        
    const updateStatusButton = $('#users-update-status');
    const selectedCountLabel = $('#selected-count'); // Make sure to add an element for this in your HTML

    // Function to update the count of selected checkboxes
    function updateSelectedCount() {
        const selectedCount = $('input[name="selection[]"]:checked').length; // Adjust the name if necessary
        const currentLanguage = '$language';
        let message;

        if (selectedCount === 1) {
            message = (currentLanguage === 'ru') ? "Выбран 1 пользователь" : "1 user selected";
        } else if (selectedCount > 1 && selectedCount < 5) {
            message = (currentLanguage === 'ru') ? `Выбрано {count} пользователя` : `{count} users selected`;
        } else {
            message = (currentLanguage === 'ru') ? `Выбрано {count} пользователей` : `{count} users selected`;
        }
        
        selectedCountLabel.text(message.replace('{count}', selectedCount));
        
        // Enable or disable the button based on selection
        if (selectedCount > 0) {
            $('#selected-block').addClass('show-flex');
        } else {
            $('#selected-block').removeClass('show-flex')
        }
    }

    // Event listener for checkbox changes
    $('input[name="selection[]"]').on('change', function() {
        updateSelectedCount();
    });

    // Initial count update
    updateSelectedCount();
            
JS;

$this->registerJs($script, yii\web\View::POS_READY);

echo $this->render('../user-roles/_js');
?>

<style>
.role-delete {
    display: inline-block;
    background: #ff7a7a;
    border-radius: 6px;
    color: white;
    margin-right: 9px;
    padding: 0 6px;
    font-size: 14px;
    text-decoration: none;
}
.role-delete:hover {
    background: #dd5757;
}
    
    
/* Setting Right Modal */
.settings-modal .btn-modal-close {
    padding: 6px 8px 7px;
    background: #f3f9fe;
    border-radius: 6px;
    display: flex;
}
.settings-modal {
    transition: transform .5s cubic-bezier(.32, .72, 0, 1);
    transform: translate3d(130%, 0, 0);
    position: fixed;
    top: 0px;
    right: 0px;
    height: 100vh;
    overflow: hidden auto;
    z-index: 10;
    box-shadow: rgba(0, 0, 0, 0.05) -5px 10px 15px;
    background: #fff;
    padding: 10px 15px;
    z-index: 1050;
    min-width: 260px;
}
.settings-modal-title:first-child {
    font-size: 18px;
    font-weight: bold;
    line-height: 1.2;
}
.settings-modal-title {
    padding: 0 0 25px;
    display: flex;
    -webkit-box-pack: justify;
    justify-content: space-between;
    -webkit-box-align: center;
    align-items: center;
}
.settings-modal.show {
    transform: translate3d(0, 0, 0);
}
.settings-modal .btn-primary {
    color: #fff;
    background-color: #2196F3;
    border-color: #2196F3;
}
.settings-modal .btn-primary:hover {
    color: #fff;
    background-color: #42A5F5;
    border-color: #42A5F5;
}
.settings-modal .btn-primary:active {
    color: #fff;
    background-color: #1976D2;
    border-color: #1976D2;
}
.settings-modal .bottom-panel {
    position: absolute;
    display: flex;
    bottom: 0;
    width: 100%;
    left: 0;
    padding: 0 12px;
}
.settings-modal .bottom-panel button {
    width: 100%;
}
.settings-modal .form-users-list {
    background: #f3f9fe;
    /* border-radius: 8px; */
    padding: 12px 16px;
    max-height: 250px;
    overflow-y: auto;
    margin: 0 -15px 0;
}
.settings-modal .form-users-list label{
    display: block;
    margin-bottom: 7px;
}
.settings-modal .form-users-list label input{
    margin-right: 6px;
}
.user-index .btn-settings {
    display: inline-block;
    float:right;
    margin-left: 3px;
}
.user-index .btn-settings, .btn-action {border-color:#e1e0df; }
.user-index .btn-settings svg {margin-top:-5px; fill:#716d66}
.user-index .btn-settings:hover svg {fill:#2196f3}
.user-index .btn-settings:hover, .user-index .btn-settings:active, .user-index .btn-action:hover, .user-index .btn-action:active {background:#f3f9fe;border-color: #d0e2f1;}
#selected-count {
    padding-top: 0.375rem;
    margin: 0 10px 0 15px;
}
#selected-block {display: flex;align-items: stretch;display:none;}
.top-control-panel {
    display:flex;justify-content:space-between;align-items:stretch;
}
.show-flex {
    display:flex !important;
}
#selected-block .dropdown-toggle {
    background-color: #f06445;color:#fff;
}
</style>
<div class="user-index">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?= Html::beginForm(['users-update'], 'post') ?>
    
    <div class="top-control-panel">
        <div style="display:flex">
            <?= Html::a(Module::t('Create User'), ['create'], ['class' => 'btn btn-success']) ?>
            <div id="selected-block">
                <p id="selected-count"></p>
                <?= $classButtonDropdown::widget([
                                'label' => Module::t('Action'),
                                'dropdown' => [
                                    'items' => [
                                        Html::a(Module::t('Change Status'), '#', [
                                            'class' => 'dropdown-item',
                                            'data' => [
                                                'bs-toggle' => 'modal',
                                                'bs-target' => '#status-modal',
                                            ],
                                        ]),
                                        Html::a(Module::t('Add Role'), '#', [
                                            'class' => 'dropdown-item',
                                            'data' => [
                                                'bs-toggle' => 'modal',
                                                'bs-target' => '#role-modal',
                                            ],
                                        ]),
                                        Html::a(Module::t('Reset password'), '#', [
                                            'class' => 'dropdown-item',
                                            'data' => [
                                                'bs-toggle' => 'modal',
                                                'bs-target' => '#reset-password-modal',
                                            ],
                                        ]),
                                    ],
                                ],
                            ]);?>
            </div>
        </div>
        <div>
            <?= Html::a('<span class="dashboard-icon"></span>', ['/user/dashboard/index'], ['class' => 'btn btn-settings']) ?>
            <span id="settings" class="btn btn-settings"><svg height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 489.802 489.802" xml:space="preserve"><path d="m20.701 281.901 32.1.2c4.8 24.7 14.3 48.7 28.7 70.5l-22.8 22.6c-8.2 8.1-8.2 21.2-.2 29.4l24.6 24.9c8.1 8.2 21.2 8.2 29.4.2l22.8-22.6c21.6 14.6 45.5 24.5 70.2 29.5l-.2 32.1c-.1 11.5 9.2 20.8 20.7 20.9l35 .2c11.5.1 20.8-9.2 20.9-20.7l.2-32.1c24.7-4.8 48.7-14.3 70.5-28.7l22.6 22.8c8.1 8.2 21.2 8.2 29.4.2l24.9-24.6c8.2-8.1 8.2-21.2.2-29.4l-22.6-22.8c14.6-21.6 24.5-45.5 29.5-70.2l32.1.2c11.5.1 20.8-9.2 20.9-20.7l.2-35c.1-11.5-9.2-20.8-20.7-20.9l-32.1-.2c-4.8-24.7-14.3-48.7-28.7-70.5l22.8-22.6c8.2-8.1 8.2-21.2.2-29.4l-24.6-24.9c-8.1-8.2-21.2-8.2-29.4-.2l-22.8 22.6c-21.6-14.6-45.5-24.5-70.2-29.5l.2-32.1c.1-11.5-9.2-20.8-20.7-20.9l-35-.2c-11.5-.1-20.8 9.2-20.9 20.7l-.3 32.1c-24.8 4.8-48.8 14.3-70.5 28.7l-22.6-22.8c-8.1-8.2-21.2-8.2-29.4-.2l-24.8 24.6c-8.2 8.1-8.2 21.2-.2 29.4l22.6 22.8c-14.6 21.6-24.5 45.5-29.5 70.2l-32.1-.2c-11.5-.1-20.8 9.2-20.9 20.7l-.2 35c-.1 11.4 9.2 20.8 20.7 20.9zm158.6-103.3c36.6-36.2 95.5-35.9 131.7.7s35.9 95.5-.7 131.7-95.5 35.9-131.7-.7-35.9-95.5.7-131.7z"/></svg></span>
        </div>
    </div>
   

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <?= GridView::widget([
        'id' => 'users',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'firstPageLabel' => Module::t('First page'),
            'lastPageLabel'  => Module::t('Last page'),
        ],
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn', 'checkboxOptions' => function($model) {
                    return ['value' => $model->id];
                },
            ],
            'id',
            'username',
            'name',
            'email:email',
            //'avatar',
            [
                'attribute' => 'city',
                'visible' => isset($columnVisibility['city']) ? $columnVisibility['city'] : true,
            ],
            [
                'attribute' => 'birthday',
                'visible' => isset($columnVisibility['birthday']) ? $columnVisibility['birthday'] : true,
                'value' => function ($model) {
                    return isset($model->birthday) ? date('d.m.Y', strtotime($model->birthday)) : '';
                }
            ],
            [
                'attribute' => 'phone',
                'visible' => isset($columnVisibility['phone']) ? $columnVisibility['phone'] : true,
            ],
            [
                'attribute' => 'sex',
                'format' => 'raw',
                'filter' => User::getSexList(),
                'value' => function ($model) {
                    return User::getSexList()[$model->sex] ?? '';
                },
                'visible' => isset($columnVisibility['sex']) ? $columnVisibility['sex'] : true,
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'filter' => User::getStatusList(),
                'value' => function ($model) {
                    return User::getStatusList()[$model->status] ?? '';
                },
                'visible' => isset($columnVisibility['sex']) ? $columnVisibility['sex'] : true,
            ],
            [
                'attribute' => 'created_by',
                'format' => 'raw',
                'value' => function ($model) {
                    $user = User::findOne($model->created_by);
                    return (!$user) ? '' : $user->name;
                },
                'visible' => isset($columnVisibility['created_by']) ? $columnVisibility['created_by'] : true,
            ],
            [
                'attribute' => 'created_at',
                'visible' => isset($columnVisibility['created_at']) ? $columnVisibility['created_at'] : true,
            ],
            [
                'attribute' => 'roles',
                'format' => 'raw',
                'filter' => Roles::getRolesList(),
                'value' => function ($model) {
                    $result = '';
                    
                    // Get all user roles
                    $roles = UserRoles::getUserRoles($model->id);
                    
                    foreach ($roles as $role) {
                        $item = Roles::findOne($role['role_id']);
                        $result .= $role['title'] . (isset($role['subject_id']) ? '('.$item->roleSubject($role['subject_id']).')' : '' );
                        $result .= ' <a href="'.Url::toRoute(['/user/user-roles/delete', 'id' => $role['id']]).'" title="'.Module::t('Delete role').'" aria-label="'.Module::t('Delete role').'" class="role-delete">X</a>';
                    }
                    
                    $result .= '<a href="'.Url::toRoute(['/user/user-roles/create', 'user_id' => $model->id, 'form'=>'_form_good_product']).'" title="'.Module::t('Add Role').'" aria-label="'.Module::t('Add Role').'" class="btn btn-sm btn-success">+</a>';
                    
                    return $result;
                
                }
            ],
            [
                'format' => 'raw',
                'value' => function ($model) use ($classButtonDropdown) {
                    return $classButtonDropdown::widget([
                        'label' => Module::t('Action'),
                        'dropdown' => [
                            'items' => [
                                ['label' => Module::t('Profile'), 'url' => Url::toRoute(['profile', 'id' => $model->id])],
                                ['label' => Module::t('Edit'), 'url' => Url::toRoute(['edit-profile', 'id' => $model->id])],
                                ['label' => Module::t('Delete'), 'url' => Url::toRoute(['delete', 'id' => $model->id])],
                                '<div class="dropdown-divider"></div>',
                                ['label' => Module::t('Appreciation'), 'url' => Url::toRoute(['/user/thanks/view', 'id' => $model->id])],
                                ['label' => Module::t('Reset password'), 'url' => Url::toRoute(['admin-reset-password', 'id' => $model->id])],
                            ],
                        ],
                    ]);
                }
            ],
        ],
    ]); ?>
    
    <!-- Modal form for status selection -->
    <?php $classModal::begin([
        'id' => 'status-modal',
        ($bootstrapVersion==3 ? 'header' : 'title') => '<h2>'.Module::t('Select Status').'</h2>',
        'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal" data-bs-dismiss="modal">' . Module::t('Close') . '</button>' . 
                        Html::submitButton(Module::t('Update Status'), ['name' => 'form-action', 'value' => 'Update Status','class' => 'btn btn-primary'])
    ]) ?>

    <?= Html::dropDownList('status', null, User::getStatusList(), ['class' => 'form-control form-select']) ?>

    <?php $classModal::end() ?>
    
    <!-- Modal form for role selection -->
    <?php $classModal::begin([
        'id' => 'role-modal',
        ($bootstrapVersion==3 ? 'header' : 'title') => '<h2>'.Module::t('Add Role').'</h2>',
        'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal" data-bs-dismiss="modal">' . Module::t('Close') . '</button>' . 
                        Html::submitButton(Module::t('Add Role'), ['name' => 'form-action', 'value' => 'Add Role', 'class' => 'btn btn-primary'])
    ]) ?>
    <div class="form-group">
        <label><?= Module::t('Role') ?></label>
        <?= Html::dropDownList('role', null, Roles::getRolesList(), ['class' => 'form-control form-select', 'id' => 'role_id']) ?>
    </div>
    <div class="form-group">
        <label><?= Module::t('Subject of the role') ?></label>
        <?= Html::input('text', 'subject_id', null, ['class' => 'form-control', 'id' => 'subject_id']) ?>
    </div>
    <div id="role_subject_group" class="form-group" style="display: none">
        <label class="control-label" for="role_subject"><?= Module::t('Subject of the role') ?></label>
        <select id="role_subject" class="form-control form-select"></select>
    </div>
    <?php $classModal::end() ?>
    
    <!-- Modal form for confirming password reset for users -->
    <?php $classModal::begin([
        'id' => 'reset-password-modal',
        ($bootstrapVersion==3 ? 'header' : 'title') => '<h2>'.Module::t('Reset password').'</h2>',
        'footer' => '' 
                        
    ]) ?>

    <p style="text-align:center"><?= Module::t('Are you sure you want to reset the passwords for the selected users?') ?></p>
    <div style="display:flex;justify-content: center;gap:4px;">
        <?= Html::submitButton(Module::t('Reset password'), ['name' => 'form-action', 'value' => 'Reset Password','class' => 'btn btn-danger']) ?>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal"><?= Module::t('Cancel') ?></button>
    </div>
    <?php $classModal::end() ?>
    
    <?= Html::endForm() ?>


    <div class="settings-modal" data-modal-name="settings">
        <div class="settings-modal-title">
            <div>Настройки</div>
            <div class="btn btn-modal-close">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="14px" height="14px" viewBox="0 0 50 50" version="1.1">
                <g id="surface1">
                <path style=" stroke:none;fill-rule:nonzero;fill:rgb(33 150 243);fill-opacity:1;" d="M 2.722656 5.144531 L 5.152344 2.75 C 6.542969 1.328125 8.867188 1.328125 10.253906 2.75 L 25.003906 17.464844 L 39.753906 2.75 C 41.144531 1.328125 43.46875 1.328125 44.855469 2.75 L 47.25 5.144531 C 48.671875 6.53125 48.671875 8.859375 47.25 10.246094 L 32.535156 24.996094 L 47.25 39.746094 C 48.671875 41.132812 48.671875 43.457031 47.25 44.847656 L 44.855469 47.277344 C 43.46875 48.664062 41.144531 48.664062 39.753906 47.277344 L 25.003906 32.527344 L 10.253906 47.277344 C 8.867188 48.664062 6.542969 48.664062 5.152344 47.277344 L 2.722656 44.847656 C 1.335938 43.457031 1.335938 41.132812 2.722656 39.746094 L 17.472656 24.996094 L 2.722656 10.246094 C 1.335938 8.859375 1.335938 6.53125 2.722656 5.144531 Z M 2.722656 5.144531 "/>
                </g>
                </svg>
            </div>
        </div>

        <div id="columnToggles">
            <?php 
            foreach (ZakharovAndrew\user\models\User::customizableColumns() as $column => $columnTitle) {?>
            <p><?= $columnTitle ?>
            <?php if (isset($columnVisibility[$column]) && $columnVisibility[$column] == true) {?>
            <div class="toggleColumn btn btn-success" data-column="<?= $column ?>" data-visibility="false"><?=  Module::t('Hide') ?></div>
            <?php } else { ?>
            <div class="toggleColumn btn btn-success" data-column="<?= $column ?>" data-visibility="true"><?=  Module::t('Show') ?></div>
            <?php } ?>
            </p>
            <?php } ?>
        </div>
    </div>
</div>