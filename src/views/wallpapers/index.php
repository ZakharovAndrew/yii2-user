<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $wallpapers array */
/* @var $currentWallpaperId int */

$this->title = 'Обои';
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="wallpaper-selection" style="display: flex; flex-wrap: wrap; gap: 20px;">
    <div class="wallpaper-item" style="flex: 1 0 30%; position: relative; cursor: pointer; border: 2px solid <?= $currentWallpaperId === 0 ? '#007bff' : '#ccc' ?>;">
        <img src="" alt="Без обоев" style="max-width: 100%; height: auto; display: block; background-color: #f0f0f0;">
        <a href="<?= \yii\helpers\Url::to(['select', 'wallpaperId' => 0]) ?>" class="btn btn-primary" style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%);">Без обоев</a>
    </div>

    <?php if (empty($wallpapers)): ?>
        <p>Нет доступных обоев.</p>
    <?php else: ?>
        <?php foreach ($wallpapers as $wallpaper): ?>
            <div class="wallpaper-item" style="flex: 1 0 30%; position: relative; cursor: pointer; border: 2px solid <?= $currentWallpaperId == $wallpaper['url'] ? '#007bff' : '#ccc' ?>;">
                <img src="<?= Html::encode($wallpaper['url']) ?>" alt="Wallpaper" style="max-width: 100%; height: auto; display: block;">
                <a href="<?= \yii\helpers\Url::to(['select', 'wallpaperId' => $wallpaper['url']]) ?>" class="btn btn-primary" style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%);">Выбрать</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.wallpaper-item {
    transition: border-color 0.3s;
}

.wallpaper-item:hover {
    border-color: #0056b3; /* Цвет при наведении */
}
</style>
