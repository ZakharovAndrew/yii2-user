<?php
use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\user\Module;

/* @var $this \yii\web\View */
/* @var $name string Name of the user who accepted the request */
?>
<div style="font-family: Arial, sans-serif; line-height: 1.6; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #333; margin-bottom: 10px;"><?= Yii::$app->name ?></h1>
        <div style="border-bottom: 2px solid #f0f0f0; margin-bottom: 20px;"></div>
    </div>
    
    <div style="background-color: #f0f8ff; padding: 20px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #4CAF50;">
        <h2 style="color: #333; margin-top: 0;">
            <?= Module::t('Friend Request Accepted') ?>
        </h2>
        
        <p style="margin-bottom: 20px; font-size: 16px;">
            <?= Module::t('Great news!') ?><br>
            <?= Module::t('{name} has accepted your friend request.', ['name' => Html::encode($name)]) ?>
        </p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="<?= Url::to(['/user/friends'], true) ?>" 
               style="display: inline-block; background-color: #2196F3; color: white; 
                      padding: 12px 30px; text-decoration: none; border-radius: 4px; 
                      font-weight: bold; font-size: 16px;">
                <?= Module::t('View Friends List') ?>
            </a>
        </div>
        
        <p style="color: #666; font-size: 14px;">
            <?= Module::t('You can now see each other\'s updates and interact in the system.') ?>
        </p>
    </div>
    
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #888; font-size: 12px;">
        <p><?= Yii::$app->name ?> &copy; <?= date('Y') ?></p>
    </div>
</div>