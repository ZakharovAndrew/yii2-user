<?php

use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\user\Module;

/* @var $this \yii\web\View */
/* @var $suggestions \ZakharovAndrew\user\models\User[] */

$this->title = Module::t('Friend Suggestions');
$this->params['breadcrumbs'][] = ['label' => Module::t('Friends'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Get current user
$currentUser = Yii::$app->user->identity;
?>
<style>
    .suggestions-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .suggestion-card {
        margin-bottom: 25px;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
        background: white;
    }
    
    .suggestion-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        border-color: #c1e0ff;
    }
    
    .suggestion-avatar {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .avatar-placeholder-large {
        width: 100%;
        height: 180px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 64px;
        font-weight: bold;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .suggestion-info {
        padding: 15px;
    }
    
    .suggestion-name {
        margin: 0 0 5px 0;
        font-size: 18px;
        font-weight: 600;
    }
    
    .suggestion-name a {
        color: #333;
        text-decoration: none;
    }
    
    .suggestion-name a:hover {
        color: #007bff;
    }
    
    .suggestion-username {
        color: #6c757d;
        font-size: 13px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }
    
    .suggestion-username i {
        margin-right: 5px;
        font-size: 12px;
    }
    
    .suggestion-details {
        margin-bottom: 15px;
        font-size: 13px;
        color: #6c757d;
    }
    
    .suggestion-details i {
        width: 18px;
        color: #999;
    }
    
    .suggestion-details div {
        margin-bottom: 5px;
    }
    
    .mutual-friends {
        background-color: #e8f4fd;
        color: #0c5460;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 12px;
        margin-bottom: 15px;
        display: inline-block;
    }
    
    .suggestion-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .suggestion-actions .btn {
        flex: 1;
        font-size: 13px;
        padding: 6px 12px;
    }
    
    .badge-mutual {
        background-color: #17a2b8;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        margin-left: 5px;
    }
    
    .refresh-suggestions {
        margin-bottom: 20px;
        text-align: right;
    }
    
    .no-suggestions {
        text-align: center;
        padding: 50px 20px;
        background: white;
        border-radius: 8px;
        border: 1px dashed #dee2e6;
    }
    
    .no-suggestions i {
        font-size: 48px;
        color: #adb5bd;
        margin-bottom: 15px;
        display: block;
    }
    
    .reason-badge {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 16px;
        padding: 2px 10px;
        font-size: 11px;
        color: #6c757d;
        display: inline-block;
        margin-right: 5px;
        margin-bottom: 5px;
    }
</style>

<div class="friend-suggestions">
    <div class="suggestions-header">
        <h1><?= Html::encode($this->title) ?></h1>
        
        <div>
            <a href="<?= Url::to(['search']) ?>" class="btn btn-primary">
                <i class="glyphicon glyphicon-search"></i> <?= Module::t('Search Friends') ?>
            </a>
            <a href="<?= Url::to(['index']) ?>" class="btn btn-default">
                <i class="glyphicon glyphicon-user"></i> <?= Module::t('My Friends') ?>
            </a>
        </div>
    </div>
    
    <!-- Refresh button -->
    <div class="refresh-suggestions">
        <a href="<?= Url::to(['suggestions']) ?>" class="btn btn-sm btn-info">
            <i class="glyphicon glyphicon-refresh"></i> <?= Module::t('Refresh Suggestions') ?>
        </a>
    </div>
    
    <?php if (empty($suggestions)): ?>
        <div class="no-suggestions">
            <i class="glyphicon glyphicon-user"></i>
            <h3><?= Module::t('No friend suggestions available at the moment.') ?></h3>
            <p class="text-muted">
                <?= Module::t('We couldn\'t find any new people to suggest right now.') ?>
            </p>
            <div style="margin-top: 20px;">
                <a href="<?= Url::to(['search']) ?>" class="btn btn-primary btn-lg">
                    <i class="glyphicon glyphicon-search"></i> <?= Module::t('Search for Friends') ?>
                </a>
            </div>
            <p class="text-muted" style="margin-top: 20px;">
                <small><?= Module::t('You can also invite friends to join!') ?></small>
            </p>
        </div>
    <?php else: ?>
        <!-- Why you might know these people -->
        <div class="panel panel-default" style="margin-bottom: 25px;">
            <div class="panel-body">
                <p class="text-muted" style="margin: 0;">
                    <i class="glyphicon glyphicon-info-sign" style="margin-right: 5px;"></i>
                    <?= Module::t('Suggestions are based on common interests, location, and mutual friends.') ?>
                </p>
            </div>
        </div>
        
        <!-- Suggestions grid -->
        <div class="row">
            <?php foreach ($suggestions as $index => $user): ?>
                <?php
                // Calculate mutual friends count (example logic - implement based on your needs)
                $mutualFriendsCount = rand(0, 5); // Placeholder - replace with actual logic
                
                // Determine suggestion reason
                $suggestionReasons = [];
                if ($user->city && $user->city == $currentUser->city) {
                    $suggestionReasons[] = Module::t('Same city');
                }
                if ($mutualFriendsCount > 0) {
                    $suggestionReasons[] = Module::t('{count} mutual friends', ['count' => $mutualFriendsCount]);
                }
                if (empty($suggestionReasons)) {
                    $suggestionReasons[] = Module::t('Based on your interests');
                }
                ?>
                
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="suggestion-card">
                        <!-- Avatar -->
                        <?php if ($user->getAvatarUrl()): ?>
                            <img src="<?= $user->getAvatarUrl() ?>" 
                                 alt="<?= Html::encode($user->name) ?>" 
                                 class="suggestion-avatar">
                        <?php else: ?>
                            <div class="avatar-placeholder-large">
                                <?= strtoupper(substr($user->name, 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- User info -->
                        <div class="suggestion-info">
                            <h4 class="suggestion-name">
                                <a href="<?= Url::to(['/user/user/profile', 'id' => $user->id]) ?>">
                                    <?= Html::encode($user->name) ?>
                                </a>
                            </h4>
                            
                            <div class="suggestion-username">
                                <i class="glyphicon glyphicon-user"></i>
                                <?= Html::encode($user->username) ?>
                            </div>
                            
                            <div class="suggestion-details">
                                <?php if ($user->city): ?>
                                    <div>
                                        <i class="glyphicon glyphicon-map-marker"></i>
                                        <?= Html::encode($user->city) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($user->birthday): ?>
                                    <div>
                                        <i class="glyphicon glyphicon-gift"></i>
                                        <?= Yii::$app->formatter->asDate($user->birthday, 'dd MMMM') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Mutual friends and reasons -->
                            <div style="margin-bottom: 12px;">
                                <?php foreach ($suggestionReasons as $reason): ?>
                                    <span class="reason-badge">
                                        <i class="glyphicon glyphicon-ok" style="font-size: 9px;"></i>
                                        <?= $reason ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Action buttons -->
                            <div class="suggestion-actions">
                                <a href="<?= Url::to(['send-request', 'id' => $user->id]) ?>" 
                                   class="btn btn-primary btn-sm"
                                   data-method="post"
                                   title="<?= Module::t('Add Friend') ?>">
                                    <i class="glyphicon glyphicon-plus"></i> <?= Module::t('Add Friend') ?>
                                </a>
                                
                                <a href="<?= Url::to(['/user/user/profile', 'id' => $user->id]) ?>" 
                                   class="btn btn-default btn-sm"
                                   title="<?= Module::t('View Profile') ?>">
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                </a>
                                
                                <a href="#" class="btn btn-default btn-sm dropdown-toggle" 
                                   data-toggle="dropdown"
                                   title="<?= Module::t('More Actions') ?>">
                                    <i class="glyphicon glyphicon-option-horizontal"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a href="#" class="suggestion-ignore" data-id="<?= $user->id ?>">
                                            <i class="glyphicon glyphicon-ban-circle"></i> 
                                            <?= Module::t('Ignore suggestion') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?= Url::to(['/user/message/create', 'to' => $user->id]) ?>">
                                            <i class="glyphicon glyphicon-envelope"></i> 
                                            <?= Module::t('Send message') ?>
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="<?= Url::to(['block', 'id' => $user->id]) ?>" 
                                           data-method="post"
                                           class="text-warning">
                                            <i class="glyphicon glyphicon-ban-circle"></i> 
                                            <?= Module::t('Block user') ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Show ad or tip every 6 suggestions -->
                <?php if (($index + 1) % 6 == 0): ?>
                    <div class="col-md-12">
                        <div class="panel panel-info" style="margin: 15px 0;">
                            <div class="panel-body">
                                <div class="media">
                                    <div class="media-left">
                                        <i class="glyphicon glyphicon-bullhorn" style="font-size: 24px; color: #17a2b8;"></i>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="media-heading"><?= Module::t('Did you know?') ?></h5>
                                        <p class="text-muted">
                                            <?= Module::t('You can search for friends by name, email, or username.') ?>
                                            <a href="<?= Url::to(['search']) ?>"><?= Module::t('Try it now!') ?></a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
            <?php endforeach; ?>
        </div>
        
        <!-- Load more button (pagination) -->
        <?php if (count($suggestions) >= 20): ?>
            <div class="text-center" style="margin: 30px 0;">
                <button class="btn btn-lg btn-default" id="load-more">
                    <i class="glyphicon glyphicon-refresh"></i> <?= Module::t('Load more suggestions') ?>
                </button>
            </div>
        <?php endif; ?>
        
        <!-- Invite friends section -->
        <div class="panel panel-default" style="margin-top: 40px;">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Module::t('Can\'t find who you\'re looking for?') ?></h3>
            </div>
            <div class="panel-body text-center">
                <p class="lead" style="margin-bottom: 20px;">
                    <?= Module::t('Invite your friends to join {appName}!', ['appName' => Yii::$app->name]) ?>
                </p>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="well well-sm">
                            <h4><?= Module::t('Share link') ?></h4>
                            <div class="input-group">
                                <input type="text" class="form-control" value="<?= Url::to(['/user/register'], true) ?>" id="invite-link" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-default copy-link" type="button" data-clipboard-target="#invite-link">
                                        <i class="glyphicon glyphicon-copy"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="well well-sm">
                            <h4><?= Module::t('Send invitation') ?></h4>
                            <a href="<?= Url::to(['/user/invite/create']) ?>" class="btn btn-primary">
                                <i class="glyphicon glyphicon-envelope"></i> <?= Module::t('Invite via email') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// JavaScript for copy to clipboard and ignore suggestion
$this->registerJs("
    $(function() {
        // Copy to clipboard
        new ClipboardJS('.copy-link');
        
        $('.copy-link').click(function() {
            $(this).tooltip({
                title: '" . Module::t('Copied!') . "',
                trigger: 'manual',
                placement: 'top'
            });
            $(this).tooltip('show');
            setTimeout(function() {
                $('.copy-link').tooltip('destroy');
            }, 1500);
        });
        
        // Ignore suggestion (you can implement AJAX here)
        $('.suggestion-ignore').click(function(e) {
            e.preventDefault();
            var card = $(this).closest('.suggestion-card');
            
            // Visual feedback
            card.fadeOut(300, function() {
                card.remove();
                
                // Show message if no suggestions left
                if ($('.suggestion-card').length === 0) {
                    location.reload(); // Reload to show empty state
                }
            });
            
            // You can add AJAX call here to permanently ignore
            // $.post('/user/friend/ignore-suggestion', {id: $(this).data('id')});
        });
        
        // Load more (you can implement AJAX pagination here)
        $('#load-more').click(function() {
            $(this).html('<i class=\"glyphicon glyphicon-refresh spinning\"></i> " . Module::t('Loading...') . "');
            // Add your AJAX call here
        });
    });
");

// Add spinning animation CSS
$this->registerCss("
    .spinning {
        animation: spin 1s infinite linear;
    }
    @keyframes spin {
        from { transform: scale(1) rotate(0deg); }
        to { transform: scale(1) rotate(360deg); }
    }
");
?>