<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $wallpapers array */

$this->title = 'Обои';
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="wallpaper-selection">
    <?php if (empty($wallpapers)): ?>
        <p>Нет доступных обоев.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($wallpapers as $wallpaper): ?>
                <li>
                    <img src="<?= Html::encode($wallpaper) ?>" alt="Wallpaper" style="max-width: 100%; height: auto;">
                    <?= Html::a('Выбрать', ['select', 'wallpaperId' => $wallpaper['id']], ['class' => 'btn btn-primary']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
