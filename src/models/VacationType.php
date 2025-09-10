<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;
use ZakharovAndrew\user\Module;

/**
 * This is the model class for table "vacation_types".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_paid
 * @property string $color
 * @property bool $is_active
 * @property int|null $max_days_per_year
 * @property int|null $min_days_per_request
 * @property int|null $max_days_per_request
 * @property bool $requires_approval
 * @property int $priority
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Vacation[] $vacations
 */
class VacationType extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%vacation_types}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['is_paid', 'is_active', 'requires_approval'], 'boolean'],
            [['max_days_per_year', 'min_days_per_request', 'max_days_per_request', 'priority'], 'integer'],
            [['max_days_per_year', 'min_days_per_request', 'max_days_per_request', 'priority'], 'default', 'value' => 0],
            [['name'], 'string', 'max' => 100],
            [['color'], 'string', 'max' => 7],
            [['color'], 'default', 'value' => '#007bff'],
            [['is_paid'], 'default', 'value' => true],
            [['is_active'], 'default', 'value' => true],
            [['requires_approval'], 'default', 'value' => true],
            [['priority'], 'default', 'value' => 0],
            
            // Валидация минимальных и максимальных значений
            [['max_days_per_year', 'min_days_per_request', 'max_days_per_request'], 'filter', 'filter' => function($value) {
                return $value === '' ? null : $value;
            }],
            [['max_days_per_year', 'min_days_per_request', 'max_days_per_request'], 'compare', 'compareValue' => 0, 'operator' => '>=', 'type' => 'number'],
            
            // Валидация приоритета
            [['priority'], 'compare', 'compareValue' => 0, 'operator' => '>=', 'type' => 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Module::t('Name'),
            'description' => Module::t('Description'),
            'is_paid' => Module::t('Is Paid'),
            'color' => Module::t('Color'),
            'is_active' => Module::t('Is Active'),
            'max_days_per_year' => Module::t('Max Days Per Year'),
            'min_days_per_request' => Module::t('Min Days Per Request'),
            'max_days_per_request' => Module::t('Max Days Per Request'),
            'requires_approval' => Module::t('Requires Approval'),
            'priority' => Module::t('Priority'),
            'created_at' => Module::t('Created At'),
            'updated_at' => Module::t('Updated At'),
        ];
    }

    /**
     * Gets query for [[Vacations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVacations()
    {
        return $this->hasMany(Vacation::class, ['type_id' => 'id']);
    }

    /**
     * Get active types
     * @return VacationType[]
     */
    public static function getActiveTypes()
    {
        return self::find()
            ->where(['is_active' => true])
            ->orderBy(['priority' => SORT_ASC, 'name' => SORT_ASC])
            ->all();
    }

    /**
     * Get types list for dropdown
     * @return array
     */
    public static function getTypesList()
    {
        return \yii\helpers\ArrayHelper::map(
            self::getActiveTypes(),
            'id',
            'name'
        );
    }

    /**
     * Get paid types
     * @return VacationType[]
     */
    public static function getPaidTypes()
    {
        return self::find()
            ->where(['is_active' => true, 'is_paid' => true])
            ->orderBy(['priority' => SORT_ASC, 'name' => SORT_ASC])
            ->all();
    }

    /**
     * Get unpaid types
     * @return VacationType[]
     */
    public static function getUnpaidTypes()
    {
        return self::find()
            ->where(['is_active' => true, 'is_paid' => false])
            ->orderBy(['priority' => SORT_ASC, 'name' => SORT_ASC])
            ->all();
    }

    /**
     * Get types that require approval
     * @return VacationType[]
     */
    public static function getApprovalRequiredTypes()
    {
        return self::find()
            ->where(['is_active' => true, 'requires_approval' => true])
            ->orderBy(['priority' => SORT_ASC, 'name' => SORT_ASC])
            ->all();
    }

    /**
     * Get types that don't require approval
     * @return VacationType[]
     */
    public static function getNoApprovalRequiredTypes()
    {
        return self::find()
            ->where(['is_active' => true, 'requires_approval' => false])
            ->orderBy(['priority' => SORT_ASC, 'name' => SORT_ASC])
            ->all();
    }

    /**
     * Check if type is available for use
     * @return bool
     */
    public function isAvailable()
    {
        return $this->is_active;
    }

    /**
     * Get color style for display
     * @return string
     */
    public function getColorStyle()
    {
        return "background-color: {$this->color}; color: " . ($this->isDarkColor() ? 'white' : 'black') . ";";
    }

    /**
     * Check if color is dark (for text contrast)
     * @return bool
     */
    protected function isDarkColor()
    {
        if (strlen($this->color) !== 7 || $this->color[0] !== '#') {
            return false;
        }

        $r = hexdec(substr($this->color, 1, 2));
        $g = hexdec(substr($this->color, 3, 2));
        $b = hexdec(substr($this->color, 5, 2));

        // Calculate luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminance < 0.5;
    }

    /**
     * Get formatted name with color badge
     * @return string
     */
    public function getFormattedName()
    {
        return \yii\helpers\Html::tag('span', '', [
                'style' => 'display: inline-block; width: 12px; height: 12px; border-radius: 2px; background-color: ' . $this->color . '; margin-right: 5px;'
            ]) . $this->name;
    }

    /**
     * Validate vacation request against type constraints
     * @param int $daysRequested
     * @param int $year
     * @return array [success, message]
     */
    public function validateRequest($daysRequested, $year = null)
    {
        if (!$this->is_active) {
            return [false, Module::t('This vacation type is not available')];
        }

        if ($this->min_days_per_request > 0 && $daysRequested < $this->min_days_per_request) {
            return [false, Module::t('Minimum {min} days required for this type', ['min' => $this->min_days_per_request])];
        }

        if ($this->max_days_per_request > 0 && $daysRequested > $this->max_days_per_request) {
            return [false, Module::t('Maximum {max} days allowed for this type', ['max' => $this->max_days_per_request])];
        }

        return [true, ''];
    }

    /**
     * Check if type has any constraints
     * @return bool
     */
    public function hasConstraints()
    {
        return $this->max_days_per_year > 0 || 
               $this->min_days_per_request > 0 || 
               $this->max_days_per_request > 0;
    }

    /**
     * Get constraint description
     * @return string
     */
    public function getConstraintsDescription()
    {
        $constraints = [];

        if ($this->max_days_per_year > 0) {
            $constraints[] = Module::t('Max {days} days per year', ['days' => $this->max_days_per_year]);
        }

        if ($this->min_days_per_request > 0) {
            $constraints[] = Module::t('Min {days} days per request', ['days' => $this->min_days_per_request]);
        }

        if ($this->max_days_per_request > 0) {
            $constraints[] = Module::t('Max {days} days per request', ['days' => $this->max_days_per_request]);
        }

        return implode(', ', $constraints);
    }

    /**
     * Before delete validation
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        // Prevent deletion if type is used in vacations
        if ($this->getVacations()->exists()) {
            Yii::$app->session->setFlash('error', Module::t('Cannot delete vacation type that is in use'));
            return false;
        }

        return true;
    }

    /**
     * After save event
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Clear cache if needed
        Yii::$app->cache->delete('vacation_types_active');
        Yii::$app->cache->delete('vacation_types_list');
    }
}