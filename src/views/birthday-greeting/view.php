<?php

use ZakharovAndrew\user\models\Roles;
use ZakharovAndrew\user\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use ZakharovAndrew\user\assets\UserAssets;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var app\models\ThanksSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Birthday congratulations to').' '.$user->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .thanks-block__info {
        margin-top:24px;
        display: flex;
        background: #f7f7f7;
        margin: -1.8rem -2.0rem;
        border-radius: 0 0 12px 12px;
        padding: 13px;
        margin-top: 25px;
    }
    .thanks-block .thanks-avatar {
        width:45px;
        height:45px;
        border-radius: 6px;
        margin-right: 10px;
    }
    .thanks-block__info_user {
        display: flex;
        flex-direction: column;
    }
    
    .thanks-block__info_user .datetime {
        color:#6e7880;
        font-size:13px;
    }
</style>
<div class="thanks-view">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <p>
        <?= Html::a(Module::t('Send congratulations'), ['send', 'id'=>$user->id], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    $items = $dataProvider->getModels();
    foreach ($items as $greeting) { ?>
        <div class="white-block thanks-block">
            <?= $greeting->message ?>
            <div class="thanks-block__info">
                <?php  $user = $greeting->getAuthor()->one(); ?>
                <img src="<?= !$user->getAvatarUrl() ?
                                Yii::$app->assetManager->getAssetUrl(UserAssets::register($this), 'images/default-avatar.png') :
                                $user->getAvatarUrl()
                            ?>" class="thanks-avatar" alt="Avatar">
                <div class="thanks-block__info_user">
                    <div><?= $user->username; ?></div>
                    <div class="datetime"><?= $greeting->getCreatedAt() ?></div>
                </div>
            </div>
        </div>
    <?php } ?>

</div>
