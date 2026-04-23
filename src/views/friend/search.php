<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\Friendship;

/* @var $this \yii\web\View */
/* @var $users \ZakharovAndrew\user\models\User[] */
/* @var $query string */

$this->title = Module::t('Search Friends');
$this->params['breadcrumbs'][] = ['label' => Module::t('Friends'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$currentUser = Yii::$app->user->identity;
?>
<style>
    .search-header {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .search-form {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
    }
    
    .search-form .form-group {
        margin-bottom: 0;
    }
    
    .search-form .btn {
        padding: 8px 20px;
    }
    
    .user-card {
        margin-bottom: 20px;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        background: white;
    }
    
    .user-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-color: #c1e0ff;
    }
    
    .user-avatar {
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
    
    .user-info h4 {
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: 600;
    }
    
    .user-info .username {
        color: #6c757d;
        font-size: 13px;
        margin-bottom: 8px;
    }
    
    .user-details {
        color: #6c757d;
        font-size: 12px;
        margin-bottom: 5px;
    }
    
    .user-details i {
        margin-right: 4px;
        width: 14px;
    }
    
    .user-actions {
        margin-top: 10px;
    }
    
    .user-actions .btn {
        margin-right: 5px;
        margin-bottom: 5px;
        padding: 4px 12px;
        font-size: 12px;
    }
    
    .badge-status {
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 500;
        margin-left: 8px;
    }
    
    .badge-friend {
        background-color: #28a745;
        color: white;
    }
    
    .badge-pending {
        background-color: #ffc107;
        color: #856404;
    }
    
    .badge-sent {
        background-color: #17a2b8;
        color: white;
    }
    
    .badge-rejected {
        background-color: #dc3545;
        color: white;
    }
    
    .empty-results {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 8px;
        border: 1px dashed #dee2e6;
    }
    
    .empty-results i {
        font-size: 48px;
        color: #adb5bd;
        margin-bottom: 15px;
        display: block;
    }
    
    .empty-results h3 {
        font-size: 20px;
        margin-bottom: 10px;
    }
    
    .search-info {
        margin-bottom: 20px;
        padding: 10px 15px;
        background-color: #e8f4fd;
        border-radius: 6px;
        color: #0c5460;
    }
</style>

<div class="friend-search">
    <div class="search-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    
    <!-- Search form -->
    <div class="search-form">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['search'],
            'options' => ['class' => 'form-inline', 'role' => 'form'],
        ]); ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="form-group" style="width: 100%;">
                    <?= Html::textInput('q', $query, [
                        'class' => 'form-control',
                        'placeholder' => Module::t('Search by name, username or email...'),
                        'style' => 'width: 100%;'
                    ]) ?>
                </div>
            </div>
            <div class="col-md-4">
                <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i> ' . Module::t('Search'), [
                    'class' => 'btn btn-primary btn-block'
                ]) ?>
            </div>
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>
    
    <!-- Search results -->
    <?php if (!empty($query)): ?>
        <div class="search-info">
            <i class="glyphicon glyphicon-info-sign"></i>
            <?= Module::t('Showing results for: "{query}"', ['query' => Html::encode($query)]) ?>
            (<?= count($users) ?> <?= Module::t('users found') ?>)
        </div>
    <?php endif; ?>
    
    <?php if (empty($users)): ?>
        <div class="empty-results">
            <i class="glyphicon glyphicon-search"></i>
            <?php if ($query): ?>
                <h3><?= Module::t('No users found') ?></h3>
                <p class="text-muted">
                    <?= Module::t('No users match your search criteria.') ?>
                </p>
                <p class="text-muted">
                    <?= Module::t('Try searching with different keywords or browse our') ?>
                    <a href="<?= Url::to(['suggestions']) ?>"><?= Module::t('friend suggestions') ?></a>.
                </p>
            <?php else: ?>
                <h3><?= Module::t('Start searching') ?></h3>
                <p class="text-muted">
                    <?= Module::t('Enter a name, username or email address to find people you know.') ?>
                </p>
                <p class="text-muted">
                    <?= Module::t('You can also check out our') ?>
                    <a href="<?= Url::to(['suggestions']) ?>"><?= Module::t('friend suggestions') ?></a>
                    <?= Module::t('to discover new people.') ?>
                </p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($users as $user): ?>
                <?php
                $friendshipStatus = $currentUser->getFriendshipStatus($user->id);
                $statusBadge = '';
                $statusText = '';
                
                if (!$friendshipStatus) {
                    $statusBadge = '';
                    $statusText = '';
                } elseif ($friendshipStatus['status'] == Friendship::STATUS_ACCEPTED) {
                    $statusBadge = 'badge-friend';
                    $statusText = Module::t('Friend');
                } elseif ($friendshipStatus['status'] == Friendship::STATUS_PENDING) {
                    if ($friendshipStatus['is_sent_by_me']) {
                        $statusBadge = 'badge-sent';
                        $statusText = Module::t('Request Sent');
                    } else {
                        $statusBadge = 'badge-pending';
                        $statusText = Module::t('Request Received');
                    }
                } elseif ($friendshipStatus['status'] == Friendship::STATUS_REJECTED) {
                    $statusBadge = 'badge-rejected';
                    $statusText = Module::t('Rejected');
                } elseif ($friendshipStatus['status'] == Friendship::STATUS_BLOCKED) {
                    $statusBadge = 'badge-rejected';
                    $statusText = Module::t('Blocked');
                }
                ?>
                
                <div class="col-md-6 col-lg-4">
                    <div class="user-card">
                        <div class="panel panel-default" style="margin: 0; border: none; box-shadow: none;">
                            <div class="panel-body">
                                <div class="media">
                                    <!-- Avatar -->
                                    <div class="media-left">
                                        <?php if ($user->getAvatarUrl()): ?>
                                            <img src="<?= $user->getAvatarUrl() ?>" 
                                                 alt="<?= Html::encode($user->name) ?>" 
                                                 class="user-avatar media-object">
                                        <?php else: ?>
                                            <div class="avatar-placeholder">
                                                <?= strtoupper(substr($user->name, 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- User info -->
                                    <div class="media-body user-info">
                                        <h4 class="media-heading">
                                            <?= Html::encode($user->name) ?>
                                            <?php if ($statusBadge): ?>
                                                <span class="badge-status <?= $statusBadge ?>"><?= $statusText ?></span>
                                            <?php endif; ?>
                                        </h4>
                                        <div class="username">
                                            <i class="glyphicon glyphicon-user"></i>
                                            <?= Html::encode($user->username) ?>
                                        </div>
                                        
                                        <?php if ($user->city): ?>
                                            <div class="user-details">
                                                <i class="glyphicon glyphicon-map-marker"></i>
                                                <?= Html::encode($user->city) ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user->birthday): ?>
                                            <div class="user-details">
                                                <i class="glyphicon glyphicon-gift"></i>
                                                <?= Yii::$app->formatter->asDate($user->birthday, 'dd MMMM') ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Action buttons -->
                                        <div class="user-actions">
                                            <?php if (!$friendshipStatus): ?>
                                                <!-- No relationship - can send request -->
                                                <a href="<?= Url::to(['send-request', 'id' => $user->id]) ?>" 
                                                   class="btn btn-primary btn-xs" 
                                                   data-method="post"
                                                   title="<?= Module::t('Add Friend') ?>">
                                                    <i class="glyphicon glyphicon-plus"></i> <?= Module::t('Add Friend') ?>
                                                </a>
                                                
                                            <?php elseif ($friendshipStatus['status'] == Friendship::STATUS_PENDING): ?>
                                                <?php if ($friendshipStatus['is_sent_by_me']): ?>
                                                    <!-- Request sent by current user -->
                                                    <button class="btn btn-info btn-xs" disabled>
                                                        <i class="glyphicon glyphicon-time"></i> <?= Module::t('Request Sent') ?>
                                                    </button>
                                                    <a href="<?= Url::to(['cancel', 'id' => $user->id]) ?>" 
                                                       class="btn btn-warning btn-xs" 
                                                       data-method="post"
                                                       data-confirm="<?= Module::t('Are you sure you want to cancel this friend request?') ?>">
                                                        <i class="glyphicon glyphicon-remove"></i> <?= Module::t('Cancel') ?>
                                                    </a>
                                                <?php else: ?>
                                                    <!-- Request received from this user -->
                                                    <button class="btn btn-warning btn-xs" disabled>
                                                        <i class="glyphicon glyphicon-time"></i> <?= Module::t('Request Received') ?>
                                                    </button>
                                                    <a href="<?= Url::to(['accept', 'id' => $user->id]) ?>" 
                                                       class="btn btn-success btn-xs" 
                                                       data-method="post">
                                                        <i class="glyphicon glyphicon-ok"></i> <?= Module::t('Accept') ?>
                                                    </a>
                                                    <a href="<?= Url::to(['reject', 'id' => $user->id]) ?>" 
                                                       class="btn btn-danger btn-xs" 
                                                       data-method="post"
                                                       data-confirm="<?= Module::t('Are you sure you want to reject this friend request?') ?>">
                                                        <i class="glyphicon glyphicon-remove"></i> <?= Module::t('Reject') ?>
                                                    </a>
                                                <?php endif; ?>
                                                
                                            <?php elseif ($friendshipStatus['status'] == Friendship::STATUS_ACCEPTED): ?>
                                                <!-- Already friends -->
                                                <button class="btn btn-success btn-xs" disabled>
                                                    <i class="glyphicon glyphicon-ok"></i> <?= Module::t('Friends') ?>
                                                </button>
                                                <a href="<?= Url::to(['remove', 'id' => $user->id]) ?>" 
                                                   class="btn btn-danger btn-xs" 
                                                   data-method="post"
                                                   data-confirm="<?= Module::t('Are you sure you want to remove this friend?') ?>">
                                                    <i class="glyphicon glyphicon-remove"></i> <?= Module::t('Remove') ?>
                                                </a>
                                                
                                            <?php elseif ($friendshipStatus['status'] == Friendship::STATUS_REJECTED): ?>
                                                <!-- Request was rejected -->
                                                <button class="btn btn-danger btn-xs" disabled>
                                                    <i class="glyphicon glyphicon-remove"></i> <?= Module::t('Request Rejected') ?>
                                                </button>
                                                <?php if ($friendshipStatus['is_sent_by_me']): ?>
                                                    <a href="<?= Url::to(['send-request', 'id' => $user->id]) ?>" 
                                                       class="btn btn-primary btn-xs" 
                                                       data-method="post">
                                                        <i class="glyphicon glyphicon-refresh"></i> <?= Module::t('Send Again') ?>
                                                    </a>
                                                <?php endif; ?>
                                                
                                            <?php elseif ($friendshipStatus['status'] == Friendship::STATUS_BLOCKED): ?>
                                                <!-- User is blocked -->
                                                <button class="btn btn-warning btn-xs" disabled>
                                                    <i class="glyphicon glyphicon-ban-circle"></i> <?= Module::t('Blocked') ?>
                                                </button>
                                                <?php if ($friendshipStatus['is_sent_by_me']): ?>
                                                    <a href="<?= Url::to(['unblock', 'id' => $user->id]) ?>" 
                                                       class="btn btn-primary btn-xs" 
                                                       data-method="post"
                                                       data-confirm="<?= Module::t('Are you sure you want to unblock this user?') ?>">
                                                        <i class="glyphicon glyphicon-ok-circle"></i> <?= Module::t('Unblock') ?>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            
                                            <!-- View profile button -->
                                            <a href="<?= Url::to(['/user/profile/view', 'id' => $user->id]) ?>" 
                                               class="btn btn-default btn-xs" 
                                               title="<?= Module::t('View Profile') ?>">
                                                <i class="glyphicon glyphicon-user"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($user->isOnline($user->id)): ?>
                                <div class="panel-footer" style="padding: 5px 15px; background-color: #e8f5e9;">
                                    <span class="text-success">
                                        <i class="glyphicon glyphicon-ok-circle"></i> 
                                        <?= Module::t('Online') ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Invite friends section -->
        <div class="panel panel-default" style="margin-top: 40px;">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Module::t('Can\'t find who you\'re looking for?') ?></h3>
            </div>
            <div class="panel-body text-center">
                <p><?= Module::t('Invite your friends to join {appName}!', ['appName' => Yii::$app->name]) ?></p>
                <a href="<?= Url::to(['/user/invite/create']) ?>" class="btn btn-primary">
                    <i class="glyphicon glyphicon-envelope"></i> <?= Module::t('Invite via email') ?>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Tooltip initialization
$this->registerJs("
    $(function () {
        $('[data-toggle=\"tooltip\"]').tooltip();
    });
");
?>