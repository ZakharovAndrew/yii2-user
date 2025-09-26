<?php
use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\user\Module;

use ZakharovAndrew\user\assets\UserAssets;
UserAssets::register($this);

/* @var $this yii\web\View */
/* @var $wallpapers array */
/* @var $currentWallpaperId int */

$this->title = Module::t('Wallpapers');
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

<?php if (Yii::$app->user->identity->isAdmin()) { ?>
    <p><a href="<?= Url::to(['/user/wallpaper-admin/index']) ?>" class="btn btn-primary"><?= Module::t('Wallpapers Management') ?></a></p>
<?php } ?>

<div class="wallpapers-list">    
    <div class="row">
        <div class="col-md-4">
            <div class="white-block  <?= $currentWallpaperId == 0 ? 'active' : '' ?>">
                <div class="wallpaper-item">
                    <div alt="Без обоев" class="no-wallpaper"></div>
                </div>
                <a href="<?= Url::to(['select', 'wallpaperId' => 0]) ?>" class="btn btn-primary">Без обоев</a>
            </div>
        </div>


        <?php if (!empty($wallpapers)): ?>
            <?php foreach ($wallpapers as $wallpaper): ?>
            <div class="col-md-4">
                <div class="white-block <?= $currentWallpaperId == $wallpaper->id ? 'active' : '' ?>">
                    <div class="wallpaper-item">
                        <img src="<?= Html::encode($wallpaper->image_url) ?>" alt="Wallpaper" class="wallpaper-image">
                    </div>
                    <div style="display:flex">
                        <div>
                            <?= $wallpaper->name ?>
                        </div>
                        <div style="margin-left: auto;">
                            <a href="<?= Url::to(['select', 'wallpaperId' => $wallpaper->id]) ?>" class="btn btn-primary"><?= Module::t('Select') ?></a>
                        </div>
                    </div>
                    
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
