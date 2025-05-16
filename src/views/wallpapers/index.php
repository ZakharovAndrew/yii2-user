<?php
use yii\helpers\Html;
use ZakharovAndrew\user\Module;

/* @var $this yii\web\View */
/* @var $wallpapers array */
/* @var $currentWallpaperId int */

$this->title = 'Обои';
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="wallpaper-selection">
    <div class="wallpaper-item <?= $currentWallpaperId === 0 ? 'active' : '' ?>">
        <img src="" alt="Без обоев" class="no-wallpaper">
        <a href="<?= \yii\helpers\Url::to(['select', 'wallpaperId' => 0]) ?>" class="btn btn-primary">Без обоев</a>
    </div>

    <?php if (empty($wallpapers)): ?>
        <p>Нет доступных обоев.</p>
    <?php else: ?>
        <?php foreach ($wallpapers as $wallpaper): ?>
            <div class="wallpaper-item <?= $currentWallpaperId == $wallpaper['url'] ? 'active' : '' ?>">
                <img src="<?= Html::encode($wallpaper['url']) ?>" alt="Wallpaper" class="wallpaper-image">
                <a href="<?= \yii\helpers\Url::to(['select', 'wallpaperId' => $wallpaper['url']]) ?>" class="btn btn-primary"><?= Module::t('Select') ?></a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.wallpaper-selection {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.wallpaper-item {
    flex: 1 0 30%;
    position: relative;
    cursor: pointer;
    border: 2px solid #ccc;
    transition: border-color 0.3s;
}

.wallpaper-item.active {
    border-color: #007bff; /* Цвет рамки для активного элемента */
}

.wallpaper-item:hover {
    border-color: #0056b3; /* Цвет при наведении */
}

.wallpaper-image,
.no-wallpaper {
    max-width: 100%;
    height: auto;
    display: block;
}

.no-wallpaper {
    background-color: #f0f0f0; /* Цвет фона для варианта "без обоев" */
}
</style>
