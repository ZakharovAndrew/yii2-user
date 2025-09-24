<?php

/** @var $user \ZakharovAndrew\user\models\User */
/** @var $wallpaper array|false */
var_dump($wallpaper);
?>

<?php if ($wallpaper): ?>
<style>
body {
    background-image: url('<?= $wallpaper['url'] ?>');
}

<?php if (isset($wallpaper['css'])): ?>
<?= $wallpaper['css'] ?>
<?php endif; ?>
</style>
<?php endif; ?>