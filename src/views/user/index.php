<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\UserRoles;
use ZakharovAndrew\user\models\Roles;
use yii\bootstrap5\Modal;

$bootstrapVersion = Yii::$app->getModule('user')->bootstrapVersion;
$classModal = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Modal";

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Users');
$this->params['breadcrumbs'][] = $this->title;

// Получаем настройки пользователя из сессии
$columnVisibility = \ZakharovAndrew\user\models\User::getColumnVisibility();

$toggleUrl = Url::to(['/user/user/toggle-column-visibility']);

$script = <<< JS
$('#users-update-status').on('click', function() {
    $('#status-modal').modal('show');
});
$('#users-add-role').on('click', function() {
    $('#role-modal').modal('show');
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
            
JS;

$this->registerJs($script, yii\web\View::POS_READY);
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
}
.user-index .btn-settings, .btn-action {border-color:#e1e0df; }
.user-index .btn-settings svg {margin-top:-5px; fill:#716d66}
.user-index .btn-settings:hover svg {fill:#2196f3}
.user-index .btn-settings:hover, .user-index .btn-settings:active, .user-index .btn-action:hover, .user-index .btn-action:active {background:#f3f9fe;border-color: #d0e2f1;}
</style>
<div class="user-index">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?= Html::beginForm(['users-update'], 'post') ?>
    <p>
        <?= Html::a(Module::t('Create User'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::button(Module::t('Change Status'), ['class' => 'btn btn-danger',  'id' => 'users-update-status']) ?>
        <?= Html::button(Module::t('Add Role'), ['class' => 'btn btn-action',  'id' => 'users-add-role']) ?>
        <span id="settings" class="btn btn-settings"><svg height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 489.802 489.802" xml:space="preserve"><path d="m20.701 281.901 32.1.2c4.8 24.7 14.3 48.7 28.7 70.5l-22.8 22.6c-8.2 8.1-8.2 21.2-.2 29.4l24.6 24.9c8.1 8.2 21.2 8.2 29.4.2l22.8-22.6c21.6 14.6 45.5 24.5 70.2 29.5l-.2 32.1c-.1 11.5 9.2 20.8 20.7 20.9l35 .2c11.5.1 20.8-9.2 20.9-20.7l.2-32.1c24.7-4.8 48.7-14.3 70.5-28.7l22.6 22.8c8.1 8.2 21.2 8.2 29.4.2l24.9-24.6c8.2-8.1 8.2-21.2.2-29.4l-22.6-22.8c14.6-21.6 24.5-45.5 29.5-70.2l32.1.2c11.5.1 20.8-9.2 20.9-20.7l.2-35c.1-11.5-9.2-20.8-20.7-20.9l-32.1-.2c-4.8-24.7-14.3-48.7-28.7-70.5l22.8-22.6c8.2-8.1 8.2-21.2.2-29.4l-24.6-24.9c-8.1-8.2-21.2-8.2-29.4-.2l-22.8 22.6c-21.6-14.6-45.5-24.5-70.2-29.5l.2-32.1c.1-11.5-9.2-20.8-20.7-20.9l-35-.2c-11.5-.1-20.8 9.2-20.9 20.7l-.3 32.1c-24.8 4.8-48.8 14.3-70.5 28.7l-22.6-22.8c-8.1-8.2-21.2-8.2-29.4-.2l-24.8 24.6c-8.2 8.1-8.2 21.2-.2 29.4l22.6 22.8c-14.6 21.6-24.5 45.5-29.5 70.2l-32.1-.2c-11.5-.1-20.8 9.2-20.9 20.7l-.2 35c-.1 11.4 9.2 20.8 20.7 20.9zm158.6-103.3c36.6-36.2 95.5-35.9 131.7.7s35.9 95.5-.7 131.7-95.5 35.9-131.7-.7-35.9-95.5.7-131.7z"/></svg></span>
    </p>

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
                'value' => function ($model) {
                    return User::getStatusList()[$model->status] ?? '';
                },
                'visible' => isset($columnVisibility['sex']) ? $columnVisibility['sex'] : true,
            ],
            [
                'attribute' => 'roles',
                'format' => 'raw',
                'filter' => \ZakharovAndrew\user\models\Roles::getRolesList(),
                'value' => function ($model) {
                    $result = '';
                    
                    // Get all user roles
                    $roles = UserRoles::getUserRoles($model->id);
                    
                    foreach ($roles as $role) {
                        $result .= $role['title'] . (isset($role['subject_id']) ? '('.$role['subject_id'].')' : '' );
                        $result .= ' <a href="'.Url::toRoute(['/user/user-roles/delete', 'id' => $role['id']]).'" title="'.Module::t('Delete role').'" aria-label="'.Module::t('Delete role').'" class="role-delete">X</a>';
                    }
                    
                    $result .= '<a href="'.Url::toRoute(['/user/user-roles/create', 'user_id' => $model->id, 'form'=>'_form_good_product']).'" title="'.Module::t('Add Role').'" aria-label="'.Module::t('Add Role').'" class="btn btn-sm btn-success">+</a>';
                    
                    return $result;
                
                }
            ],
            
            //'created_at',
            //'updated_at',
            
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, User $model, $key, $index, $column) {
                    if ($action == 'view') {
                        return Url::toRoute(['profile', 'id' => $model->id]);
                    }
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>
    
    <!-- Modal form for status selection -->
    <?php $classModal::begin([
        'id' => 'status-modal',
        ($bootstrapVersion==3 ? 'header' : 'title') => '<h2>'.Module::t('Select Status').'</h2>',
        'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">' . Module::t('Close') . '</button>' . 
                        Html::submitButton(Module::t('Update Status'), ['name' => 'form-action', 'value' => 'Update Status','class' => 'btn btn-primary'])
    ]) ?>

    <?= Html::dropDownList('status', null, User::getStatusList(), ['class' => 'form-control form-select']) ?>

    <?php $classModal::end() ?>
    
    <?php $classModal::begin([
        'id' => 'role-modal',
        ($bootstrapVersion==3 ? 'header' : 'title') => '<h2>'.Module::t('Select Role').'</h2>',
        'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">' . Module::t('Close') . '</button>' . 
                        Html::submitButton(Module::t('Add Role'), ['name' => 'form-action', 'value' => 'Add Role', 'class' => 'btn btn-primary'])
    ]) ?>
    <?= Html::dropDownList('role', null, Roles::getRolesList(), ['class' => 'form-control form-select']) ?>
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
            <div class="toggleColumn btn btn-success" data-column="<?= $column ?>" data-visibility="false">Скрыть</div>
            <?php } else { ?>
            <div class="toggleColumn btn btn-success" data-column="<?= $column ?>" data-visibility="true">Показать</div>
            <?php } ?>
            </p>
            <?php } ?>
        </div>
    </div>
</div>