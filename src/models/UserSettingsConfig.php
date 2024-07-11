<?php

namespace ZakharovAndrew\user\models;

use Yii;

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
            'title' => 'Title',
            'code' => 'Code',
            'type' => 'Type',
            'access_level' => 'Access Level',
            'values' => 'Values',
        ];
    }
}
