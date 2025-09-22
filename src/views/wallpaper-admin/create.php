<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\user\models\Wallpaper */
/* @var $allRoles array */

$this->title = Module::t('Create Wallpaper');
$this->params['breadcrumbs'][] = ['label' => Module::t('Wallpapers Management'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wallpaper-admin-create">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h1><?= Html::encode($this->title) ?></h1>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                            
                            <?= $form->field($model, 'image_url')->textInput(['maxlength' => true])
                                ->hint(Module::t('Enter image URL or upload file below')) ?>
                            
                            <?= $form->field($model, 'image_file')->fileInput()
                                ->hint(Module::t('Supported formats: JPG, PNG, GIF. Max size: 2MB')) ?>
                                
                            <?= $form->field($model, 'position')->textInput(['type' => 'number'])
                                ->hint(Module::t('Lower numbers appear first')) ?>
                                
                            <?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'roles')->widget(Select2::class, [
                                'data' => $allRoles,
                                'options' => ['multiple' => true, 'placeholder' => Module::t('Select roles...')],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'tags' => true,
                                ],
                            ])->hint(Module::t('Leave empty to make wallpaper available for all roles')) ?>
                            
                            <?= $form->field($model, 'css_settings')->textarea(['rows' => 4])
                                ->hint(Module::t('CSS for desktop devices. Example: background-size: cover; opacity: 0.8;')) ?>
                                
                            <?= $form->field($model, 'mobile_css_settings')->textarea(['rows' => 4])
                                ->hint(Module::t('CSS for mobile devices. Will override desktop CSS on mobile.')) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton(Module::t('Create'), ['class' => 'btn btn-success']) ?>
                        <?= Html::a(Module::t('Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
