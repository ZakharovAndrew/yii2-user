<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\Module;

/** @var yii\web\View $this */
/** @var UserDeputy $model */
/** @var User $user */
/** @var User[] $availableUsers */

$this->title = Module::t('Add Deputy for {user}', ['user' => $user->name]);
$this->params['breadcrumbs'][] = ['label' => Module::t('Users'), 'url' => ['/user/user/index']];
$this->params['breadcrumbs'][] = ['label' => $user->name, 'url' => ['/user/user/view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = ['label' => Module::t('Deputies'), 'url' => ['index', 'user_id' => $user->id]];
$this->params['breadcrumbs'][] = Module::t('Add');

?>
<div class="user-deputy-create">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <div class="user-deputy-form">
        <?php $form = ActiveForm::begin(); ?>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'deputy_user_id')->dropDownList(
                            \yii\helpers\ArrayHelper::map($availableUsers, 'id', function($user) {
                                return $user->name . ' (' . $user->email . ')';
                            }),
                            [
                                'prompt' => Module::t('Select deputy...'),
                                'class' => 'form-control form-select select2'
                            ]
                        ) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'valid_from')->textInput(['type' => 'date']) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'valid_to')->textInput(['type' => 'date']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <?= Html::submitButton(Module::t('Add Deputy'), ['class' => 'btn btn-success']) ?>
                    <?= Html::a(Module::t('Cancel'), ['index', 'user_id' => $user->id], ['class' => 'btn btn-secondary']) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>

<?php

// CSS/JS Select2
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$placeholder = Module::t('Select deputy...');

// init Select2
$this->registerJs(<<<JS
    $('.select2').select2({
        allowClear: true,
        placeholder: '$placeholder',
        width: '100%',
        theme: 'bootstrap'
    });
JS
);
