<?php

namespace ZakharovAndrew\user\models;

/**
 * This is the model class for table "user_activity".
 *
 * @property int $id
 * @property int $user_id
 * @property string $date_at
 * @property string|null $start_activity
 * @property string|null $stop_activity
 */
class UserActivity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_activity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['date_at', 'start_activity', 'stop_activity'], 'safe']
        ];
    }
    
    /**
      * Logging the beginning and end of activity
      */
    public static function setActivity()
    {
        if (!\Yii::$app->user->isGuest){
            // trying to find activity for today
            $model = static::find()
                    ->where(['date_at' => date('Y-m-d')])
                    ->andWhere(['user_id' => \Yii::$app->user->id])
                    ->one();
            
            // new activity today
            if (!$model) {
                $model = new UserActivity([
                    'date_at' => date('Y-m-d'),
                    'user_id' => \Yii::$app->user->id,
                    'start_activity' => date('Y-m-d H:i:s')
                ]);
            }
            
            $model->stop_activity = date('Y-m-d H:i:s');
            $model->save();
        }
    }
}
