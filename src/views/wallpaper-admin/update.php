<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use ZakharovAndrew\user\Module;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\user\models\Wallpaper */
/* @var $allRoles array */

$this->title = Module::t('Update Wallpaper: {name}', ['name' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Module::t('Wallpapers Management'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('Update');
?>

<style>
    .user-form .select2-container--default .select2-selection--single,
    .user-form .select2-container .select2-selection--multiple
    {
        background: #f5f8fa;
        border: none;
    }
</style>

<div class="wallpaper-admin-update">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h1><?= Html::encode($this->title) ?></h1>
                </div>
                <div class="card-body">
                    <?php if ($model->image_url): ?>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label><?= Module::t('Current Image:') ?></label><br>
                            <?= Html::img($model->image_url, [
                                'style' => 'max-width: 300px; max-height: 200px; object-fit: cover; border: 1px solid #ddd;'
                            ]) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                            
                            <?= $form->field($model, 'image_url')->textInput(['maxlength' => true])
                                ->hint(Module::t('Enter image URL or upload file below')) ?>
                                                            
                            <?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'roles')->dropDownList($allRoles, [
                                    'options' => ['multiple' => true, 'placeholder' => Module::t('Select roles...')],
                                    'class' => 'form-control form-select select2'
                                ])
                                ->hint(Module::t('Leave empty to make wallpaper available for all roles')) ?>
                            
                            <?= $form->field($model, 'css_settings')->textarea(['rows' => 4]) ?>
                                
                            <?= $form->field($model, 'mobile_css_settings')->textarea(['rows' => 4]) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton(Module::t('Update'), ['class' => 'btn btn-primary']) ?>
                        <?= Html::a(Module::t('Cancel'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// CSS/JS Select2
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
// init Select2
$this->registerJs(<<<JS
    $('.select2').select2({
        allowClear: true,
        
    });
JS
);