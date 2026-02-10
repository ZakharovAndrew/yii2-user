<?php
use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\user\Module;

/* @var $this \yii\web\View */
/* @var $name string Name of the user who sent friend request */
/* @var $user \ZakharovAndrew\user\models\User User who received the request */
?>
<div style="font-family: Arial, sans-serif; line-height: 1.6; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #333; margin-bottom: 10px;"><?= Yii::$app->name ?></h1>
        <div style="border-bottom: 2px solid #f0f0f0; margin-bottom: 20px;"></div>
    </div>
    
    <div style="background-color: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h2 style="color: #333; margin-top: 0;">
            <?= Module::t('New Friend Request') ?>
        </h2>
        
        <p style="margin-bottom: 20px;">
            <?= Module::t('Hello!') ?><br>
            <?= Module::t('User {name} has sent you a friend request.', ['name' => Html::encode($name)]) ?>
        </p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="<?= Url::to(['/user/friends/requests'], true) ?>" 
               style="display: inline-block; background-color: #4CAF50; color: white; 
                      padding: 12px 30px; text-decoration: none; border-radius: 4px; 
                      font-weight: bold; font-size: 16px;">
                <?= Module::t('View Friend Request') ?>
            </a>
        </div>
        
        <p style="color: #666; font-size: 14px;">
            <?= Module::t('You can accept or reject this request in your friends section.') ?>
        </p>
    </div>
    
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #888; font-size: 12px;">
        <p>
            <?= Module::t('This email was sent automatically. Please do not reply to this email.') ?>
        </p>
        <p>
            <?= Module::t('If you did not expect this email, you can safely ignore it.') ?>
        </p>
        <p>
            <?= Module::t('To manage your email notifications, visit your account settings.') ?>
        </p>
    </div>
    
    <div style="text-align: center; margin-top: 20px; color: #999; font-size: 12px;">
        <p>&copy; <?= date('Y') ?> <?= Yii::$app->name ?>. <?= Module::t('All rights reserved.') ?></p>
    </div>
</div>