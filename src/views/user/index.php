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
<div class="user-index">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <p>
        <?= Html::a(Module::t('Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',

            //'password_reset_token',
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
                'value' => function ($model) {
                    $result = '';
                    
                    // Get all user roles
                    $roles = UserRoles::getUserRoles($model->id);
                    
                    foreach ($roles as $role) {
                        $result .= $role['title'] . '<a href="'.Url::toRoute(['/user/user-roles/delete', 'id' => $role['id']]).'" title="'.Module::t('Delete role').'" aria-label="'.Module::t('Delete role').'" >X</a>';
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
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
