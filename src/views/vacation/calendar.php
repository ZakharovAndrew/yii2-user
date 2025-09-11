<?php

use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\Vacation;

/* @var $this yii\web\View */
/* @var $vacations array */

$this->title = Module::t('Vacation Calendar');
$this->params['breadcrumbs'][] = $this->title;

// Формируем события для календаря
$events = [];
foreach ($vacations as $vacation) {
    $events[] = [
        'title' => $vacation->user->name . ' - ' . $vacation->type->name,
        'start' => $vacation->start_date,
        'end' => date('Y-m-d', strtotime($vacation->end_date . ' +1 day')),
        'color' => $vacation->type->color,
        'url' => Url::to(['view', 'id' => $vacation->id]),
        'extendedProps' => [
            'user' => $vacation->user->name,
            'type' => $vacation->type->name,
            'days' => $vacation->days_count,
            'status' => $vacation->getStatusLabel()['label'],
            'userId' => $vacation->user_id,
            'typeId' => $vacation->type_id,
        ]
    ];
}

$eventsJson = json_encode($events);
?>

<div class="vacation-calendar">

    <?php if (Yii::$app->getModule('user')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><?= Module::t('Filters') ?></h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label><?= Module::t('Employee') ?></label>
                        <select class="form-control" id="user-filter">
                            <option value=""><?= Module::t('All Employees') ?></option>
                            <?php
                            $users = \yii\helpers\ArrayHelper::map(
                                \ZakharovAndrew\user\models\User::find()
                                    ->where(['!=', 'status', \ZakharovAndrew\user\models\User::STATUS_DELETED])
                                    ->orderBy(['name' => SORT_ASC])
                                    ->all(),
                                'id',
                                'name'
                            );
                            foreach ($users as $id => $name): ?>
                                <option value="<?= $id ?>" <?= $id == $userId ? 'selected' : '' ?>>
                                    <?= Html::encode($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><?= Module::t('Vacation Type') ?></label>
                        <select class="form-control" id="type-filter">
                            <option value=""><?= Module::t('All Types') ?></option>
                            <?php
                            $types = \yii\helpers\ArrayHelper::map(
                                \ZakharovAndrew\user\models\VacationType::find()
                                    ->where(['is_active' => true])
                                    ->orderBy(['name' => SORT_ASC])
                                    ->all(),
                                'id',
                                'name'
                            );
                            foreach ($types as $id => $name): ?>
                                <option value="<?= $id ?>" <?= $id == $typeId ? 'selected' : '' ?>>
                                    <?= Html::encode($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button class="btn btn-primary btn-sm" id="apply-filters">
                        <?= Module::t('Apply Filters') ?>
                    </button>
                    <button class="btn btn-secondary btn-sm" id="clear-filters">
                        <?= Module::t('Clear Filters') ?>
                    </button>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title"><?= Module::t('Legend') ?></h5>
                </div>
                <div class="card-body">
                    <?php foreach ($types as $id => $name): 
                        $type = \ZakharovAndrew\user\models\VacationType::findOne($id);
                        if ($type): ?>
                            <div class="legend-item mb-2">
                                <span class="legend-color" style="background-color: <?= $type->color ?>;"></span>
                                <span class="legend-text"><?= Html::encode($name) ?></span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
// Подключаем FullCalendar CSS и JS
$this->registerCssFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/main.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/fullcalendar6.1.19/locales/ru.js');

// CSS стили
$this->registerCss(<<<CSS
.legend-item {
    display: flex;
    align-items: center;
}

.legend-color {
    width: 16px;
    height: 16px;
    border-radius: 3px;
    margin-right: 8px;
    display: inline-block;
}

.legend-text {
    font-size: 14px;
}

.fc-event {
    cursor: pointer;
    border: none;
    font-size: 12px;
    padding: 2px 4px;
}

.fc-daygrid-event-dot {
    display: none;
}

.vacation-tooltip {
    position: absolute;
    background: white;
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 1000;
    max-width: 300px;
}

.card {
    margin-bottom: 20px;
}
CSS
);

$script = <<< JS
// Глобальные переменные
var calendar;
var allEvents = $eventsJson;
        console.log($eventsJson);

// Инициализация календаря
function initCalendar() {
        console.log('asd');
    var calendarEl = document.getElementById('calendar');
    
        console.log('asd11');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ru',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: allEvents,
        eventClick: function(info) {
            window.location.href = info.event.url;
        },
        eventMouseEnter: function(info) {
            var event = info.event;
            var tooltip = document.createElement('div');
            tooltip.className = 'vacation-tooltip';
            tooltip.innerHTML = `
                <strong>\${event.extendedProps.user}</strong><br>
                \${event.extendedProps.type}<br>
                \${event.extendedProps.days} дней<br>
                Статус: \${event.extendedProps.status}
            `;
            
            document.body.appendChild(tooltip);
            
            var rect = info.el.getBoundingClientRect();
            tooltip.style.top = (rect.top + window.scrollY - tooltip.offsetHeight - 10) + 'px';
            tooltip.style.left = (rect.left + window.scrollX) + 'px';
            
            info.el.setAttribute('data-tooltip', tooltip);
        },
        eventMouseLeave: function(info) {
            $(".vacation-tooltip").remove();
        }
    });
    console.log('asd');
    console.log(calendar.render());
}

// Применение фильтров
function applyFilters() {
    var userId = document.getElementById('user-filter').value;
    var typeId = document.getElementById('type-filter').value;
    
    var filteredEvents = allEvents.filter(function(event) {
        var matchUser = true;
        var matchType = true;
        
        if (userId) {
            matchUser = event.extendedProps.userId == userId;
        }
        
        if (typeId) {
            matchType = event.extendedProps.typeId == typeId;
        }
        
        return matchUser && matchType;
    });
    
    calendar.removeAllEvents();
    calendar.addEventSource(filteredEvents);
}

// Очистка фильтров
function clearFilters() {
    document.getElementById('user-filter').value = '';
    document.getElementById('type-filter').value = '';
    
    calendar.removeAllEvents();
    calendar.addEventSource(allEvents);
}

initCalendar();
    
// Назначаем обработчики кнопкам
document.getElementById('apply-filters').addEventListener('click', applyFilters);
document.getElementById('clear-filters').addEventListener('click', clearFilters);

// Назначаем обработчики изменения select
document.getElementById('user-filter').addEventListener('change', applyFilters);
document.getElementById('type-filter').addEventListener('change', applyFilters);

// Делаем функции глобальными для отладки
window.applyFilters = applyFilters;
window.clearFilters = clearFilters;
JS;

$this->registerJs($script, yii\web\View::POS_READY);