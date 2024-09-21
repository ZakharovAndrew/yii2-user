<?php

use yii\helpers\Html;
use ZakharovAndrew\user\Module;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\models\UserSettingsConfig;
use ZakharovAndrew\user\assets\UserAssets;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\User $model */

$this->title = Module::t('Edit Profile');
?>
<div class="edit-profile">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>
    
    <div class="user-form">

        <?php $form = ActiveForm::begin(); 
        if (!empty($model->birthday)) {
            $model->birthday = date('Y-m-d', strtotime($model->birthday));
        }
        ?>
        
        <div class="white-block">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'birthday')->input('date') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'sex')->dropDownList(User::getSexList()) ?>
                </div>
            </div>
        </div>
        <div class="white-block">
        <?php foreach ($settings as $setting) {?>
            <div class="form-group">
                <label><?= $setting->title ?></label>
                <?php
                if ($setting->type == 2 && !empty($setting->getValues())) {
                    echo Html::dropDownList( $setting->code, $setting->getUserSettingValue(), $setting->getValues(), [
                            'id' => 'settings-'.$setting->code,
                            'class' => 'form-control',
                            'prompt' => ''
                        ]);
                } else {
                    // determine the type
                    $inputType = 'text';
                    if ($setting->type == UserSettingsConfig::TYPE_TIME) {
                        $inputType = 'time';
                    } else if ($setting->type == UserSettingsConfig::TYPE_DATE) {
                        $inputType = 'date';
                    }
                    echo Html::input($inputType, $setting->code, $setting->getUserSettingValue(), ['id' => 'settings-'.$setting->code, 'class' => 'form-control']);
                }?>
            </div>
        <?php } ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
    
    

</div>