<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\UserRoles;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Users');
$this->params['breadcrumbs'][] = $this->title;
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
</style>
<div class="user-index">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <p>
        <?= Html::a(Module::t('Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'firstPageLabel' => Module::t('First page'),
            'lastPageLabel'  => Module::t('Last page'),
        ],
        'columns' => [
            'id',
            'username',
            'name',
            'email:email',
            //'avatar',
            //'city',
            //'birthday',
            [
                'attribute' => 'sex',
                'format' => 'raw',
                'filter' => User::getSexList(),
                'value' => function ($model) {
                    return User::getSexList()[$model->sex] ?? '';
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    return User::getStatusList()[$model->status] ?? '';
                }
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
                    
                    $result .= '<a href="'.Url::toRoute(['/user/user-roles/create', 'user_id' => $model->id, 'form'=>'_form_good_product']).'" title="'.Module::t('Add role').'" aria-label="'.Module::t('Add role').'" class="btn btn-sm btn-success">+</a>';
                    
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


</div>
