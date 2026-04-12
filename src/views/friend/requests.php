<?php

use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\Friendship;

/* @var $this \yii\web\View */
/* @var $requests \ZakharovAndrew\user\models\Friendship[] */
/* @var $title string */
/* @var $currentType string */

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Module::t('Friends'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Determine which user to display based on request type
$isSentRequests = $currentType === 'sent';
$isRejected = $currentType === 'rejected';
$currentUser = Yii::$app->user->identity;
?>
<style>
    .requests-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .request-tabs {
        margin-bottom: 25px;
    }
    
    .request-tabs .nav-tabs li a {
        color: #555;
        transition: all 0.3s ease;
    }
    
    .request-tabs .nav-tabs li.active a {
        color: #007bff;
        font-weight: 500;
    }
    
    .request-item {
        margin-bottom: 15px;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        background: white;
    }
    
    .request-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-color: #c1e0ff;
    }
    
    .request-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .avatar-placeholder {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: bold;
    }
    
    .request-info h4 {
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: 600;
    }
    
    .request-info .username {
        color: #6c757d;
        font-size: 13px;
        margin-bottom: 8px;
    }
    
    .request-time {
        color: #6c757d;
        font-size: 12px;
        margin-bottom: 5px;
    }
    
    .request-time i {
        margin-right: 4px;
        font-size: 11px;
    }
    
    .request-actions {
        margin-top: 10px;
    }
    
    .request-actions .btn {
        margin-right: 5px;
        margin-bottom: 5px;
        padding: 4px 12px;
        font-size: 13px;
    }
    
    .badge-status {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    
    .badge-pending {
        background-color: #ffc107;
        color: #856404;
    }
    
    .badge-accepted {
        background-color: #28a745;
        color: white;
    }
    
    .badge-rejected {
        background-color: #dc3545;
        color: white;
    }
    
    .badge-blocked {
        background-color: #6c757d;
        color: white;
    }
    
    .empty-requests {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 8px;
        border: 1px dashed #dee2e6;
    }
    
    .empty-requests i {
        font-size: 48px;
        color: #adb5bd;
        margin-bottom: 15px;
        display: block;
    }
    
    .empty-requests h3 {
        font-size: 20px;
        margin-bottom: 10px;
    }
</style>

<div class="friend-requests">
    <div class="requests-header">
        <h1><?= Html::encode($this->title) ?></h1>
        
        <div>
            <a href="<?= Url::to(['index']) ?>" class="btn btn-default">
                <i class="glyphicon glyphicon-user"></i> <?= Module::t('My Friends') ?>
            </a>
            <a href="<?= Url::to(['search']) ?>" class="btn btn-primary">
                <i class="glyphicon glyphicon-search"></i> <?= Module::t('Find Friends') ?>
            </a>
        </div>
    </div>
    
    <!-- Tabs for different request types -->
    <div class="request-tabs">
        <ul class="nav nav-tabs">
            <li class="<?= $currentType === 'received' ? 'active' : '' ?>">
                <a href="<?= Url::to(['requests', 'type' => 'received']) ?>">
                    <i class="glyphicon glyphicon-inbox"></i> 
                    <?= Module::t('Received') ?>
                    <?php $count = $currentUser->getPendingReceivedRequests()->count(); ?>
                    <?php if ($count > 0): ?>
                        <span class="badge"><?= $count ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="<?= $currentType === 'sent' ? 'active' : '' ?>">
                <a href="<?= Url::to(['requests', 'type' => 'sent']) ?>">
                    <i class="glyphicon glyphicon-send"></i> 
                    <?= Module::t('Sent') ?>
                </a>
            </li>
            <li class="<?= $currentType === 'rejected' ? 'active' : '' ?>">
                <a href="<?= Url::to(['requests', 'type' => 'rejected']) ?>">
                    <i class="glyphicon glyphicon-ban-circle"></i> 
                    <?= Module::t('Rejected') ?>
                </a>
            </li>
        </ul>
    </div>
    
    <?php if (empty($requests)): ?>
        <div class="empty-requests">
            <?php if ($currentType === 'received'): ?>
                <i class="glyphicon glyphicon-inbox"></i>
                <h3><?= Module::t('No pending friend requests') ?></h3>
                <p class="text-muted">
                    <?= Module::t('You don\'t have any pending friend requests at the moment.') ?>
                </p>
                <a href="<?= Url::to(['search']) ?>" class="btn btn-primary">
                    <i class="glyphicon glyphicon-search"></i> <?= Module::t('Find Friends') ?>
                </a>
            <?php elseif ($currentType === 'sent'): ?>
                <i class="glyphicon glyphicon-send"></i>
                <h3><?= Module::t('No sent requests') ?></h3>
                <p class="text-muted">
                    <?= Module::t('You haven\'t sent any friend requests yet.') ?>
                </p>
                <a href="<?= Url::to(['search']) ?>" class="btn btn-primary">
                    <i class="glyphicon glyphicon-search"></i> <?= Module::t('Find Friends') ?>
                </a>
            <?php else: ?>
                <i class="glyphicon glyphicon-ban-circle"></i>
                <h3><?= Module::t('No rejected requests') ?></h3>
                <p class="text-muted">
                    <?= Module::t('You don\'t have any rejected friend requests.') ?>
                </p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="requests-list">
            <?php foreach ($requests as $request): ?>
                <?php 
                // Determine which user to display
                $displayUser = $isSentRequests ? $request->friend : $request->user;
                $isMyRequest = $request->user_id == Yii::$app->user->id;
                $statusClass = '';
                $statusText = '';
                
                if ($request->status == Friendship::STATUS_PENDING) {
                    $statusClass = 'badge-pending';
                    $statusText = Module::t('Pending');
                } elseif ($request->status == Friendship::STATUS_ACCEPTED) {
                    $statusClass = 'badge-accepted';
                    $statusText = Module::t('Accepted');
                } elseif ($request->status == Friendship::STATUS_REJECTED) {
                    $statusClass = 'badge-rejected';
                    $statusText = Module::t('Rejected');
                } else {
                    $statusClass = 'badge-blocked';
                    $statusText = Module::t('Blocked');
                }
                ?>
                
                <div class="request-item">
                    <div class="panel panel-default" style="margin: 0; border: none; box-shadow: none;">
                        <div class="panel-body">
                            <div class="media">
                                <!-- Avatar -->
                                <div class="media-left">
                                    <?php if ($displayUser->getAvatarUrl()): ?>
                                        <img src="<?= $displayUser->getAvatarUrl() ?>" 
                                             alt="<?= Html::encode($displayUser->name) ?>" 
                                             class="request-avatar media-object">
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <?= strtoupper(substr($displayUser->name, 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- User info -->
                                <div class="media-body request-info">
                                    <h4 class="media-heading">
                                        <?= Html::encode($displayUser->name) ?>
                                        <small class="username">
                                            <i class="glyphicon glyphicon-user"></i> 
                                            <?= Html::encode($displayUser->username) ?>
                                        </small>
                                    </h4>
                                    
                                    <div class="request-time">
                                        <i class="glyphicon glyphicon-time"></i>
                                        <?php if ($isRejected && $request->responded_at): ?>
                                            <?= Module::t('Responded') ?>: 
                                            <?= Yii::$app->formatter->asRelativeTime($request->responded_at) ?>
                                        <?php else: ?>
                                            <?= Module::t('Requested') ?>: 
                                            <?= Yii::$app->formatter->asRelativeTime($request->created_at) ?>
                                        <?php endif; ?>
                                        
                                        <span class="badge-status <?= $statusClass ?>" style="margin-left: 10px;">
                                            <?= $statusText ?>
                                        </span>
                                    </div>
                                    
                                    <?php if ($displayUser->city): ?>
                                        <div class="request-time">
                                            <i class="glyphicon glyphicon-map-marker"></i>
                                            <?= Html::encode($displayUser->city) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Action buttons -->
                                    <div class="request-actions">
                                        <?php if ($currentType === 'received'): ?>
                                            <!-- Received requests - can accept/reject -->
                                            <a href="<?= Url::to(['accept', 'id' => $request->user_id]) ?>" 
                                               class="btn btn-success btn-sm" 
                                               title="<?= Module::t('Accept Request') ?>"
                                               data-method="post">
                                                <i class="glyphicon glyphicon-ok"></i> <?= Module::t('Accept') ?>
                                            </a>
                                            <a href="<?= Url::to(['reject', 'id' => $request->user_id]) ?>" 
                                               class="btn btn-danger btn-sm" 
                                               title="<?= Module::t('Reject Request') ?>"
                                               data-method="post"
                                               data-confirm="<?= Module::t('Are you sure you want to reject this friend request?') ?>">
                                                <i class="glyphicon glyphicon-remove"></i> <?= Module::t('Reject') ?>
                                            </a>
                                            <a href="<?= Url::to(['/user/profile/view', 'id' => $displayUser->id]) ?>" 
                                               class="btn btn-default btn-sm" 
                                               title="<?= Module::t('View Profile') ?>">
                                                <i class="glyphicon glyphicon-eye-open"></i> <?= Module::t('View Profile') ?>
                                            </a>
                                            
                                        <?php elseif ($currentType === 'sent'): ?>
                                            <!-- Sent requests - can cancel -->
                                            <a href="<?= Url::to(['cancel', 'id' => $request->friend_id]) ?>" 
                                               class="btn btn-warning btn-sm" 
                                               title="<?= Module::t('Cancel Request') ?>"
                                               data-method="post"
                                               data-confirm="<?= Module::t('Are you sure you want to cancel this friend request?') ?>">
                                                <i class="glyphicon glyphicon-remove"></i> <?= Module::t('Cancel Request') ?>
                                            </a>
                                            <a href="<?= Url::to(['/user/profile/view', 'id' => $displayUser->id]) ?>" 
                                               class="btn btn-default btn-sm" 
                                               title="<?= Module::t('View Profile') ?>">
                                                <i class="glyphicon glyphicon-eye-open"></i> <?= Module::t('View Profile') ?>
                                            </a>
                                            
                                        <?php elseif ($currentType === 'rejected'): ?>
                                            <!-- Rejected requests - can resend or block -->
                                            <a href="<?= Url::to(['send-request', 'id' => $displayUser->id]) ?>" 
                                               class="btn btn-primary btn-sm" 
                                               title="<?= Module::t('Send Request Again') ?>"
                                               data-method="post">
                                                <i class="glyphicon glyphicon-refresh"></i> <?= Module::t('Send Again') ?>
                                            </a>
                                            <a href="<?= Url::to(['block', 'id' => $displayUser->id]) ?>" 
                                               class="btn btn-warning btn-sm" 
                                               title="<?= Module::t('Block User') ?>"
                                               data-method="post"
                                               data-confirm="<?= Module::t('Are you sure you want to block this user?') ?>">
                                                <i class="glyphicon glyphicon-ban-circle"></i> <?= Module::t('Block') ?>
                                            </a>
                                            <a href="<?= Url::to(['/user/profile/view', 'id' => $displayUser->id]) ?>" 
                                               class="btn btn-default btn-sm" 
                                               title="<?= Module::t('View Profile') ?>">
                                                <i class="glyphicon glyphicon-eye-open"></i> <?= Module::t('View Profile') ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Quick actions info -->
        <div class="panel panel-info" style="margin-top: 25px;">
            <div class="panel-body">
                <div class="media">
                    <div class="media-left">
                        <i class="glyphicon glyphicon-info-sign" style="font-size: 24px; color: #17a2b8;"></i>
                    </div>
                    <div class="media-body">
                        <h5 class="media-heading"><?= Module::t('About Friend Requests') ?></h5>
                        <p class="text-muted" style="margin-bottom: 0;">
                            <?= Module::t('When you accept a friend request, you will be able to see each other\'s updates and interact in the system.') ?>
                        </p>
                    </div>
                </div>
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