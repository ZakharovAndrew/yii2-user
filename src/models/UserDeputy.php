<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\Module;

/**
 * This is the model class for table "user_deputies".
 *
 * @property int $id
 * @property int $user_id
 * @property int $deputy_user_id
 * @property int $created_by
 * @property string $created_at
 * @property string $valid_from
 * @property string|null $valid_to
 * @property int $is_active
 *
 * @property User $user
 * @property User $deputyUser
 * @property User $createdBy
 */
class UserDeputy extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_deputies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'deputy_user_id', 'valid_from'], 'required'],
            [['user_id', 'deputy_user_id', 'created_by', 'is_active'], 'integer'],
            [['created_at', 'valid_from', 'valid_to'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['deputy_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['deputy_user_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            ['is_active', 'default', 'value' => self::STATUS_ACTIVE],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Module::t('User'),
            'deputy_user_id' => Module::t('Deputy'),
            'created_by' => Module::t('Created by'),
            'created_at' => Module::t('Creation Date'),
            'valid_from' => Module::t('Valid from'),
            'valid_to' => Module::t('Valid to'),
            'is_active' => Module::t('Status'),
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
     * Gets query for [[DeputyUser]].
     */
    public function getDeputyUser()
    {
        return $this->hasOne(User::class, ['id' => 'deputy_user_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Before save event
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = date('Y-m-d H:i:s');
            if (empty($this->created_by)) {
                $this->created_by = Yii::$app->user->id;
            }
        }
        return parent::beforeSave($insert);
    }

    /**
     * Get active status list
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => Module::t('Active'),
            self::STATUS_INACTIVE => Module::t('Inactive'),
        ];
    }

    /**
     * Check if deputy relation is currently active
     */
    public function isCurrentlyActive()
    {
        $now = date('Y-m-d H:i:s');
        return $this->is_active == self::STATUS_ACTIVE &&
               $this->valid_from <= $now &&
               ($this->valid_to === null || $this->valid_to >= $now);
    }
}