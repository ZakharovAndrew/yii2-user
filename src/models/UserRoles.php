<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\Module;

/**
 * This is the model class for table "user_roles".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $role_id
 * @property string|null $note
 * @property string|null $created_at
 */
class UserRoles extends \yii\db\ActiveRecord
{
    public $title;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_roles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'role_id', 'subject_id'], 'integer'],
            [['created_at'], 'safe'],
            [['note'], 'string', 'max' => 500],
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
            'role_id' => Module::t('Role'),
            'note' => Module::t('Note'),
            'created_at' => 'Created At',
        ];
    }
    
    /**
     * Get all user roles
     * 
     * @param integer $user_id User ID
     * @return mixed
     */
    public static function getUserRoles($user_id)
    {
        return Yii::$app->cache->getOrSet('get_users_roles_'.$user_id, function () use ($user_id) {
            return static::find()
                ->select('user_roles.*, roles.title, roles.code')
                ->leftJoin('roles', 'user_roles.role_id = roles.id')
                ->where(['user_roles.user_id' => $user_id])
                ->asArray()
                ->all();
        }, 10);
    }
    
    /**
     * Get a list of users who have a role
     * 
     * @param $user
     * @param $role
     * @param $subject_id
     * @return mixed
     */
    public static function getUsersListByRoleSubject($role, $subject_id = null)
    {
        $model = static::find()
                ->select('user_roles.user_id')
                ->leftJoin('roles', 'user_roles.role_id = roles.id')
                ->andWhere(['roles.code' => $role]);
        
        // if a subject is specified, we take it into account
        if ($subject_id) {
            $model->andWhere(["or", ["user_roles.subject_id" => $subject_id], ["subject_id" => null]]);
        }
         
        return $model->cache(600)->asArray()->all();
    }
    
    /**
     * Checking that the user has a role
     * 
     * @param $user
     * @param string $role - user role
     * @param int|null $subject_id
     * @return bool
     */
    public static function hasRole($user, $role, $subject_id = null)
    {
        $model = static::find()
                ->leftJoin('roles', 'user_roles.role_id = roles.id')
                ->where(['user_roles.user_id' => $user->id])
                ->andWhere(['roles.code' => $role]);
        
        // if a subject is specified, we take it into account
        if ($subject_id) {
            $model->andWhere(["or", ["user_roles.subject_id" => $subject_id], ["subject_id" => null]]);
        }
        
        return $model->count() > 0;
    }
    
    /**
     * Reset cache after saving
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->cache->delete('get_users_roles_'.$this->user_id);
        Yii::$app->cache->delete('get_roles_by_user_'.$this->user_id);
    }
}
