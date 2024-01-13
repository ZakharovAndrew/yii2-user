<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\Module;
use \yii\helpers\ArrayHelper;

/**
 * This is the model class for table "roles".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $created_at
 */
class Roles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'roles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['description'], 'string'],
            [['created_at'], 'safe'],
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
            'description' => Module::t('Description'),
            'code' => Module::t('Code'),
            'created_at' => 'Created At',
        ];
    }
    
    public static function getRolesList()
    {
        $arr = static::find()
                ->select(['id', 'title'])
                ->asArray()
                ->all();
        
        return ArrayHelper::map($arr, 'id', 'title');
    }
}
