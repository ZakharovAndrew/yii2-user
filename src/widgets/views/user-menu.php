<?php
use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\user\Module;

/** @var $user \ZakharovAndrew\user\models\User */
?>

<li class="nav-item dropdown ms-auto">
    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" 
       data-bs-toggle="dropdown" aria-expanded="false">
        <?php if ($user->getAvatarUrl()): ?>
            <?= Html::img($user->getAvatarUrl(), [
                'class' => 'rounded-circle me-2',
                'style' => 'width: 32px; height: 32px; object-fit: cover;',
                'alt' => $user->name,
            ]) ?>
        <?php else: ?>
            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" 
                 style="width: 32px; height: 32px;">
                <span class="text-white fw-bold"><?= mb_substr($user->name, 0, 1) ?></span>
            </div>
        <?php endif; ?>
        
        <span class="d-none d-md-inline"><?= Html::encode($user->name) ?></span>
    </a>
    
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <?= Html::a(
                '<i class="fas fa-image me-2"></i>' . Module::t('Wallpapers'),
                ['/user/wallpaper/index'],
                ['class' => 'dropdown-item']
            ) ?>
        </li>
        <li>
            <?= Html::a(
                '<i class="fas fa-user me-2"></i>' . Module::t('Profile'),
                ['/user/user/profile'],
                ['class' => 'dropdown-item']
            ) ?>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <?= Html::a(
                '<i class="fas fa-sign-out-alt me-2"></i>' . Module::t('Logout'),
                ['/user/user/logout'],
                [
                    'class' => 'dropdown-item',
                    'data' => [
                        'method' => 'post',
                    ]
                ]
            ) ?>
        </li>
    </ul>
</li>