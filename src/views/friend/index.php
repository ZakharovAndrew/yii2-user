<?php

use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\user\Module;

/* @var $this \yii\web\View */
/* @var $friends \ZakharovAndrew\user\models\User[] */
/* @var $friendsCount integer */

$this->title = Module::t('My Friends');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .friend-item {
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    .friend-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .friend-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
    }
    .avatar-placeholder {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
        font-weight: bold;
    }
    .friend-info h4 {
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: 600;
    }
    .friend-info .username {
        color: #6c757d;
        font-size: 13px;
        margin-bottom: 8px;
    }
    .friend-city {
        color: #6c757d;
        font-size: 12px;
        margin-bottom: 10px;
    }
    .friend-city i {
        margin-right: 4px;
    }
    .friend-actions .btn {
        padding: 3px 8px;
        font-size: 12px;
    }
    .badge-friends {
        background-color: #28a745;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 14px;
        margin-left: 10px;
    }
    .friends-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
    }
</style>

<div class="friend-index">
    <div class="friends-header">
        <h1>
            <?= Html::encode($this->title) ?>
            <span class="badge badge-friends"><?= $friendsCount ?></span>
        </h1>
        
        <div>
            <a href="<?= Url::to(['search']) ?>" class="btn btn-primary">
                <i class="glyphicon glyphicon-search"></i> <?= Module::t('Find Friends') ?>
            </a>
            <a href="<?= Url::to(['suggestions']) ?>" class="btn btn-success">
                <i class="glyphicon glyphicon-user"></i> <?= Module::t('Suggestions') ?>
            </a>
        </div>
    </div>

    <?php if (empty($friends)): ?>
        <div class="alert alert-info">
            <i class="glyphicon glyphicon-info-sign"></i>
            <?= Module::t('You don\'t have any friends yet.') ?>
            <a href="<?= Url::to(['search']) ?>" class="alert-link"><?= Module::t('Find friends') ?></a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($friends as $friend): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="panel panel-default friend-item">
                        <div class="panel-body">
                            <div class="media">
                                <div class="media-left">
                                    <?php if ($friend->getAvatarUrl()): ?>
                                        <img src="<?= $friend->getAvatarUrl() ?>" 
                                             alt="<?= Html::encode($friend->name) ?>" 
                                             class="friend-avatar media-object">
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <?= strtoupper(substr($friend->name, 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="media-body friend-info">
                                    <h4 class="media-heading">
                                        <?= Html::encode($friend->name) ?>
                                    </h4>
                                    <div class="username">
                                        <i class="glyphicon glyphicon-user"></i>
                                        <?= Html::encode($friend->username) ?>
                                    </div>
                                    
                                    <?php if ($friend->city): ?>
                                        <div class="friend-city">
                                            <i class="glyphicon glyphicon-map-marker"></i>
                                            <?= Html::encode($friend->city) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="friend-actions btn-group btn-group-sm">
                                        <a href="<?= Url::to(['/user/profile/view', 'id' => $friend->id]) ?>" 
                                           class="btn btn-default" 
                                           title="<?= Module::t('View Profile') ?>"
                                           data-toggle="tooltip">
                                            <i class="glyphicon glyphicon-user"></i>
                                        </a>
                                        
                                        <a href="<?= Url::to(['remove', 'id' => $friend->id]) ?>" 
                                           class="btn btn-danger" 
                                           title="<?= Module::t('Remove Friend') ?>"
                                           data-toggle="tooltip"
                                           data-method="post"
                                           data-confirm="<?= Module::t('Are you sure you want to remove this friend?') ?>">
                                            <i class="glyphicon glyphicon-remove"></i>
                                        </a>
                                        
                                        <a href="<?= Url::to(['block', 'id' => $friend->id]) ?>" 
                                           class="btn btn-warning" 
                                           title="<?= Module::t('Block User') ?>"
                                           data-toggle="tooltip"
                                           data-method="post"
                                           data-confirm="<?= Module::t('Are you sure you want to block this user?') ?>">
                                            <i class="glyphicon glyphicon-ban-circle"></i>
                                        </a>
                                        
                                        <a href="#" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                            <i class="glyphicon glyphicon-envelope"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a href="<?= Url::to(['/user/message/create', 'to' => $friend->id]) ?>">
                                                    <i class="glyphicon glyphicon-envelope"></i> 
                                                    <?= Module::t('Send Message') ?>
                                                </a>
                                            </li>
                                            <?php if ($friend->isBirthdayToday()): ?>
                                                <li>
                                                    <a href="<?= Url::to(['/user/birthday-greeting/send', 'id' => $friend->id]) ?>">
                                                        <i class="glyphicon glyphicon-gift"></i> 
                                                        <?= Module::t('Send Birthday Greeting') ?>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($friend->isOnline($friend->id)): ?>
                            <div class="panel-footer" style="padding: 5px 15px; background-color: #e8f5e9;">
                                <span class="text-success">
                                    <i class="glyphicon glyphicon-ok-circle"></i> 
                                    <?= Module::t('Online') ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Friends stats -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= Module::t('Friends Statistics') ?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="row text-center">
                            <div class="col-sm-4">
                                <div class="well well-sm">
                                    <h3><?= $friendsCount ?></h3>
                                    <small><?= Module::t('Total Friends') ?></small>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="well well-sm">
                                    <h3><?= count(array_filter($friends, function($f) { 
                                        return $f->city == Yii::$app->user->identity->city; 
                                    })) ?></h3>
                                    <small><?= Module::t('Friends from your city') ?></small>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="well well-sm">
                                    <h3><?= count(array_filter($friends, function($f) { 
                                        return $f->isOnline($f->id); 
                                    })) ?></h3>
                                    <small><?= Module::t('Friends Online') ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Инициализация tooltips
$this->registerJs("
    $(function () {
        $('[data-toggle=\"tooltip\"]').tooltip();
    });
");
?>