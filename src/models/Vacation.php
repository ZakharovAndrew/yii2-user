<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;
use ZakharovAndrew\user\Module;

/**
 * This is the model class for table "vacations".
 *
 * @property int $id
 * @property int $user_id
 * @property int $type_id
 * @property string $start_date
 * @property string $end_date
 * @property int $days_count
 * @property int $status
 * @property string|null $comment
 * @property int|null $created_by
 * @property int|null $approved_by
 * @property string|null $approved_at
 * @property int|null $rejected_by
 * @property string|null $rejected_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 * @property VacationType $type
 * @property User $createdBy
 * @property User $approvedBy
 * @property User $rejectedBy
 */
class Vacation extends ActiveRecord
{
    const STATUS_REQUESTED = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;
    const STATUS_CANCELLED = 4;
    const STATUS_COMPLETED = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%vacations}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type_id', 'start_date', 'end_date'], 'required'],
            [['user_id', 'type_id', 'days_count', 'status', 'approved_by', 'rejected_by'], 'integer'],
            [['start_date', 'end_date', 'approved_at', 'rejected_at'], 'safe'],
            [['comment'], 'string'],
            ['end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>='],
            ['days_count', 'default', 'value' => function($model) {
                return $model->calculateDaysCount();
            }],
            ['status', 'default', 'value' => self::STATUS_REQUESTED],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            //[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => VacationType::class, 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Module::t('Employee'),
            'type_id' => Module::t('Vacation Type'),
            'start_date' => Module::t('Start Date'),
            'end_date' => Module::t('End Date'),
            'days_count' => Module::t('Days Count'),
            'status' => Module::t('Status'),
            'comment' => Module::t('Comment'),
            'created_by' => Module::t('Created By'),
            'approved_by' => Module::t('Approved By'),
            'approved_at' => Module::t('Approval Date'),
            'rejected_by' => Module::t('Rejected By'),
            'rejected_at' => Module::t('Rejection Date'),
            'created_at' => Module::t('Created At'),
            'updated_at' => Module::t('Updated At'),
        ];
    }

    /**
     * Gets query for [[User]].
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Type]].
     */
    public function getType()
    {
        return $this->hasOne(VacationType::class, ['id' => 'type_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[ApprovedBy]].
     */
    public function getApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'approved_by']);
    }

    /**
     * Gets query for [[RejectedBy]].
     */
    public function getRejectedBy()
    {
        return $this->hasOne(User::class, ['id' => 'rejected_by']);
    }

    /**
     * Calculate number of vacation days
     */
    public function calculateDaysCount()
    {
        if ($this->start_date && $this->end_date) {
            $start = new \DateTime($this->start_date);
            $end = new \DateTime($this->end_date);
            $interval = $start->diff($end);
            return $interval->days + 1; // Including both start and end dates
        }
        return 0;
    }

    /**
     * Get status list
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_REQUESTED => Module::t('Requested'),
            self::STATUS_APPROVED => Module::t('Approved'),
            self::STATUS_REJECTED => Module::t('Rejected'),
            self::STATUS_COMPLETED => Module::t('Completed'),
        ];
    }

    /**
     * Get status label with color
     */
    public function getStatusLabel()
    {
        $statuses = [
            self::STATUS_REQUESTED => [
                'label' => Module::t('Requested'), 
                'class' => 'badge badge-warning'
            ],
            self::STATUS_APPROVED => [
                'label' => Module::t('Approved'), 
                'class' => 'badge badge-success'
            ],
            self::STATUS_REJECTED => [
                'label' => Module::t('Rejected'), 
                'class' => 'badge badge-danger'
            ],
            self::STATUS_COMPLETED => [
                'label' => Module::t('Completed'), 
                'class' => 'badge badge-info'
            ],
        ];
        
        return $statuses[$this->status] ?? ['label' => 'Неизвестно', 'class' => 'badge badge-dark'];
    }

    /**
     * Check if vacation is active
     */
    public function isActive()
    {
        $today = date('Y-m-d');
        return $this->status == self::STATUS_APPROVED && 
               $this->start_date <= $today && 
               $this->end_date >= $today;
    }

    /**
     * Check if vacation is upcoming
     */
    public function isUpcoming()
    {
        $today = date('Y-m-d');
        return $this->status == self::STATUS_APPROVED && 
               $this->start_date > $today;
    }

    /**
     * Approve vacation
     */
    public function approve($approvedBy)
    {
        $this->status = self::STATUS_APPROVED;
        $this->approved_by = $approvedBy;
        $this->approved_at = date('Y-m-d H:i:s');
        $this->rejected_by = null;
        $this->rejected_at = null;
        $this->cancelled_by = null;
        $this->cancelled_at = null;
        return $this->save();
    }

    /**
     * Reject vacation
     */
    public function reject($rejectedBy, $comment = null)
    {
        $this->status = self::STATUS_REJECTED;
        $this->rejected_by = $rejectedBy;
        $this->rejected_at = date('Y-m-d H:i:s');
        $this->approved_by = null;
        $this->approved_at = null;
        $this->cancelled_by = null;
        $this->cancelled_at = null;
        if ($comment) {
            $this->comment = $comment;
        }
        return $this->save();
    }

    /**
     * Complete vacation (automatically when end date passes)
     */
    public function complete()
    {
        if ($this->status == self::STATUS_APPROVED && $this->end_date < date('Y-m-d')) {
            $this->status = self::STATUS_COMPLETED;
            return $this->save();
        }
        return false;
    }

    /**
     * Get approval time in human readable format
     */
    public function getApprovalTime()
    {
        if ($this->approved_at && $this->created_at) {
            $created = new \DateTime($this->created_at);
            $approved = new \DateTime($this->approved_at);
            $interval = $created->diff($approved);
            
            if ($interval->days > 0) {
                return $interval->days . ' дней';
            } elseif ($interval->h > 0) {
                return $interval->h . ' часов';
            } else {
                return $interval->i . ' минут';
            }
        }
        return null;
    }

    /**
     * Get days until vacation starts
     */
    public function getDaysUntilStart()
    {
        if ($this->start_date && $this->status == self::STATUS_APPROVED) {
            $today = new \DateTime();
            $start = new \DateTime($this->start_date);
            
            if ($start > $today) {
                $interval = $today->diff($start);
                return $interval->days;
            }
        }
        return 0;
    }

    /**
     * Get vacation duration in working days (excluding weekends)
     */
    public function getWorkingDaysCount()
    {
        if ($this->start_date && $this->end_date) {
            $start = new \DateTime($this->start_date);
            $end = new \DateTime($this->end_date);
            $workingDays = 0;
            
            while ($start <= $end) {
                $dayOfWeek = $start->format('N');
                if ($dayOfWeek < 6) { // 1-5 Monday-Friday
                    $workingDays++;
                }
                $start->modify('+1 day');
            }
            
            return $workingDays;
        }
        return 0;
    }
    
    /**
     * Check if vacation can be edited by current user
     * @return bool
     */
    public function canBeEdited()
    {
        $userId = Yii::$app->user->id;

        // Пользователь может редактировать только свои отпуски
        if ($this->user_id != $userId) {
            return false;
        }

        // Можно редактировать только запрошенные отпуски
        return $this->status == self::STATUS_REQUESTED;
    }

    /**
     * Check if user can view this vacation
     * @return bool
     */
    public function canView()
    {
        $userId = Yii::$app->user->id;

        // Пользователь может видеть свои отпуски
        if ($this->user_id == $userId) {
            return true;
        }

        // Менеджеры/админы могут видеть все отпуски
        // Здесь должна быть проверка прав доступа
        return Yii::$app->user->can('manageVacations');
    }

    /**
     * Check if dates are valid
     * @return bool
     */
    public function validateDates()
    {
        if ($this->start_date && $this->end_date) {
            $start = new \DateTime($this->start_date);
            $end = new \DateTime($this->end_date);
            $today = new \DateTime();

            // Дата начала не может быть в прошлом
            if ($start < $today) {
                $this->addError('start_date', Module::t('Start date cannot be in the past'));
                return false;
            }

            // Дата окончания должна быть после даты начала
            if ($end <= $start) {
                $this->addError('end_date', Module::t('End date must be after start date'));
                return false;
            }
        }

        return true;
    }
    
    /**
     * Get vacations for calendar
     * @param int|null $userId
     * @param int|null $typeId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public static function getForCalendar($userId = null, $typeId = null, $startDate = null, $endDate = null)
    {
        $query = self::find()
            ->with(['user', 'type'])/*
            ->where(['status' => self::STATUS_APPROVED])*/;

        if ($userId) {
            $query->andWhere(['user_id' => $userId]);
        }

        if ($typeId) {
            $query->andWhere(['type_id' => $typeId]);
        }

        if ($startDate) {
            $query->andWhere(['>=', 'start_date', $startDate]);
        }

        if ($endDate) {
            $query->andWhere(['<=', 'end_date', $endDate]);
        }

        return $query->orderBy(['start_date' => SORT_ASC])->all();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // Автоматически рассчитываем количество дней
        if ($this->start_date && $this->end_date) {
            $this->days_count = $this->calculateDaysCount();
        }

        return $this->validateDates();
    }
}