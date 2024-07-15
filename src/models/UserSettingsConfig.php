<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\Module;

/**
 * This is the model class for table "user_settings_config".
 *
 * @property int $id
 * @property string $title
 * @property string $code
 * @property int|null $type
 * @property int|null $access_level
 * @property string|null $values
 */
class UserSettingsConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_settings_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'code'], 'required'],
            [['type', 'access_level'], 'integer'],
            [['values'], 'string'],
            [['title', 'code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => Module::t('Title'),
            'code' => Module::t('Code'),
            'type' => Module::t('Type'),
            'access_level' => 'Access Level',
            'values' => Module::t('Values'),
        ];
    }
    
    public static function getTypeOfSettings()
    {
        return [
            1 => Module::t('Integer'),
            2 => Module::t('String'),
            3 => Module::t('Date'),
        ];
    }
    
}
