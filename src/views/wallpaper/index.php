<?php
use yii\helpers\Html;
use ZakharovAndrew\user\Module;

use ZakharovAndrew\user\assets\UserAssets;
UserAssets::register($this);

/* @var $this yii\web\View */
/* @var $wallpapers array */
/* @var $currentWallpaperId int */

$this->title = 'Обои';
?>
<style>
    .wallpaper-item {
        background: #f6f6f6;
        border-radius: 8px;
        position: relative;
        margin: 0 0 4px;
        aspect-ratio: 1 / 0.7;
        overflow: hidden;
        width: 100%;
        height: auto;
    }
    .wallpaper-image, .no-wallpaper {
        /* max-width: 100%; */
        /* height: auto; */
        display: block;
        /* max-height: 233px; */
        background-size: cover;
        height: -webkit-fill-available;
    }
    .wallpapers-list .white-block {
        padding: 1rem 1.25rem;
    }
    .wallpapers-list .active {
        border-color: #007bff;
    }
</style>
    
<?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

<div class="wallpapers-list">    
    <div class="row">
        <div class="col-md-4">
            <div class="white-block  <?= $currentWallpaperId == 0 ? 'active' : '' ?>">
                <div class="wallpaper-item">
                    <div alt="Без обоев" class="no-wallpaper"></div>
                </div>
                <a href="<?= \yii\helpers\Url::to(['select', 'wallpaperId' => 0]) ?>" class="btn btn-primary">Без обоев</a>
            </div>
        </div>


        <?php if (!empty($wallpapers)): ?>
            <?php foreach ($wallpapers as $wallpaperId => $wallpaper): ?>
            <div class="col-md-4">
                <div class="white-block <?= $currentWallpaperId == $wallpaperId ? 'active' : '' ?>">
                    <div class="wallpaper-item">
                        <img src="<?= Html::encode($wallpaper) ?>" alt="Wallpaper" class="wallpaper-image">
                    </div>
                    <a href="<?= \yii\helpers\Url::to(['select', 'wallpaperId' => $wallpaperId]) ?>" class="btn btn-primary"><?= Module::t('Select') ?></a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
