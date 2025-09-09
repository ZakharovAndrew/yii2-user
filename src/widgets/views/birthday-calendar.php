<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="birthday-calendar-widget">
    <div class="calendar-header">
        <h4><?= Html::encode($title) ?></h4>
        <div class="calendar-period">
            <?= Yii::$app->formatter->asDate('now', 'medium') ?> - 
            <?= Yii::$app->formatter->asDate('+1 month', 'medium') ?>
        </div>
    </div>

    <div class="calendar-weeks">
        <?php foreach ($calendar as $week): ?>
            <div class="calendar-week <?= $week['week_number'] == date('W') ? 'current-week' : '' ?>">
                <div class="week-header">
                    <span class="week-range">
                        Week <?= $week['week_number'] ?>: 
                        <?= Yii::$app->formatter->asDate($week['start_date'], 'MMM d') ?> - 
                        <?= Yii::$app->formatter->asDate($week['end_date'], 'MMM d') ?>
                    </span>
                </div>
                
                <div class="week-days">
                    <?php foreach ($week['days'] as $day): ?>
                        <div class="calendar-day 
                            <?= $day['is_today'] ? 'today' : '' ?>
                            <?= $day['is_past'] ? 'past' : '' ?>
                            <?= $day['is_future'] ? 'future' : '' ?>">
                            
                            <div class="day-header">
                                <span class="day-name"><?= $day['day_name'] ?></span>
                                <span class="day-number"><?= $day['day_number'] ?></span>
                            </div>
                            
                            <?php if (!empty($day['birthdays'])): ?>
                                <div class="birthdays-container">
                                    <?php 
                                    $birthdaysToShow = array_slice($day['birthdays'], 0, $maxUsersPerDay);
                                    $extraBirthdays = count($day['birthdays']) - $maxUsersPerDay;
                                    ?>
                                    
                                    <?php foreach ($birthdaysToShow as $birthday): ?>
                                        <div class="birthday-item">
                                            <div class="user-name">
                                                <?= Html::a(
                                                    Html::encode($birthday['user']->name),
                                                    ['/user/admin/update', 'id' => $birthday['user']->id],
                                                    [
                                                        'title' => 'View profile',
                                                        'class' => 'user-link'
                                                    ]
                                                ) ?>
                                            </div>
                                            
                                            <?php if ($showAge): ?>
                                                <div class="user-age">
                                                    <?= $birthday['age'] ?> years
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="birthday-date">
                                                <?= Yii::$app->formatter->asDate($birthday['birthday'], 'medium') ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <?php if ($extraBirthdays > 0): ?>
                                        <div class="extra-birthdays">
                                            +<?= $extraBirthdays ?> more birthday<?= $extraBirthdays > 1 ? 's' : '' ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="no-birthdays">No birthdays</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
