<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\Module;

/** @var yii\web\View $this */
/** @var UserDeputy $model */
/** @var User $user */

$this->title = Module::t('Update Deputy for {user}', ['user' => $user->name]);
$this->params['breadcrumbs'][] = ['label' => Module::t('Users'), 'url' => ['/user/user/index']];
$this->params['breadcrumbs'][] = ['label' => $user->name, 'url' => ['/user/user/view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = ['label' => Module::t('Deputies'), 'url' => ['index', 'user_id' => $user->id]];
$this->params['breadcrumbs'][] = Module::t('Update');

?>
<div class="user-deputy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-deputy-form">
        <?php $form = ActiveForm::begin(); ?>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'deputy_user_id')->textInput([
                            'disabled' => true,
                            'value' => $model->deputyUser ? $model->deputyUser->name . ' (' . $model->deputyUser->email . ')' : ''
                        ]) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'valid_from')->textInput(['type' => 'date']) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'valid_to')->textInput(['type' => 'date']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <?= Html::submitButton(Module::t('Update'), ['class' => 'btn btn-primary']) ?>
                    <?= Html::a(Module::t('Cancel'), ['index', 'user_id' => $user->id], ['class' => 'btn btn-secondary']) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>