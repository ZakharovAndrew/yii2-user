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
 * @property string|null $parameters
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
            [['parameters', 'created_at'], 'safe'],
            [['title', 'code', 'function_to_get_all_subjects'], 'string', 'max' => 255],
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
            'parameters' => Module::t('Parameters'),
            'function_to_get_all_subjects' => Module::t('Function to get all role subjects'),
            'created_at' => 'Created At',
        ];
    }
    
    /**
     * Get all roles
     * 
     * @return array
     */
    public static function getRolesList()
    {
        $arr = static::find()
                ->select(['id', 'title'])
                ->asArray()
                ->all();
        
        return ArrayHelper::map($arr, 'id', 'title');
    }
    
    /**
     * Get all user roles with available controller actions
     * 
     * @param integer $user_id User ID
     * @return mixed
     */
    public static function getRolesByUserId($user_id)
    {
        return Yii::$app->cache->getOrSet('get_roles_by_user_'.$user_id, function () use ($user_id) {
            return static::find()
                ->select('roles.*')
                ->leftJoin('user_roles', 'user_roles.role_id = roles.id')
                ->where(['user_roles.user_id' => $user_id])
                ->all();
        }, 10);
    }
    
    public function getParametersList()
    {
        return json_decode($this->parameters) ?? [];
    }
    
    public function roleSubject($subject)
    {
        if (!empty($this->function_to_get_all_subjects) && is_callable($this->function_to_get_all_subjects)) {
            // function name
            $func = $this->function_to_get_all_subjects;
            // exec function
            return $func()[$subject] ?? $subject;
        }
        
        return $subject;
    }
    
    /**
     * Getting role subjects
     * @return array
     */
    public function getSubjects()
    {
        if (!empty($this->function_to_get_all_subjects) && is_callable($this->function_to_get_all_subjects)) {
            // function name
            $func = $this->function_to_get_all_subjects;
            // exec function
            return $func();
        }
        
        return [];
    }
}
