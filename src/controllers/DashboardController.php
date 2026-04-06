<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use ZakharovAndrew\user\controllers\ParentController;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\UserSettings;
use ZakharovAndrew\user\models\UserSettingsConfig;
use ZakharovAndrew\user\models\User;
use \yii\helpers\ArrayHelper;

class DashboardController extends ParentController
{

    public $controller_id = 1006;
    
    /**
     * Main dashboard
     *
     * @return string
     */
    public function actionIndex($setting = null, $setting2 = null, $status = [])
    {
        if (!Yii::$app->user->identity->hasRole('admin')) {
            throw new NotFoundHttpException(Module::t('The requested page does not exist.'));
        }
        
        $settings = ArrayHelper::map(UserSettingsConfig::find()->all(), 'code', 'title');

        $setting_name = (!$setting || !isset($settings[$setting])) ? [array_key_first($settings)] : $setting;
        $setting2_name = (!$setting2 || !isset($settings[$setting2])) ? null : $setting2;
        
        
        
        if (!$setting2_name) {
            $data = User::find()->alias('u')
                    ->select(['cnt' => 'count(*)', new \yii\db\Expression("
                        case 
                        when us.values is null or us.values = '' 
                        then 'Value not set' 
                        else us.values 
                        end as setting_value")])
                    ->leftJoin(UserSettingsConfig::tableName(). ' s',
                        ['s.code' => $setting_name]
                    )
                    ->leftJoin(UserSettings::tableName(). ' us',
                        "us.setting_config_id = s.id AND us.user_id = u.id"  
                    )
                    ->where(count($status) ==0 ? [] : ['u.status' => $status])
                    ->groupBy(["setting_value"])
                    ->orderBy("cnt DESC")
                    ->asArray()->all();
        } else {
            $data = User::find()->alias('u')
                    ->select(['cnt' => 'count(*)', new \yii\db\Expression("
                        case 
                        when us.values is null or us.values = '' 
                        then 'Value not set' 
                        else us.values 
                        end as setting_value")
                        , new \yii\db\Expression("
                        case 
                        when us2.values is null or us2.values = '' 
                        then 'Value not set' 
                        else us2.values 
                        end as setting2_value")])
                    ->leftJoin(UserSettingsConfig::tableName(). ' s',
                        ['s.code' => $setting_name]
                    )
                    ->leftJoin(UserSettings::tableName(). ' us',
                        "us.setting_config_id = s.id AND us.user_id = u.id"  
                    )
                    ->leftJoin(UserSettingsConfig::tableName(). ' s2',
                        ['s2.code' => $setting2_name]
                    )
                    ->leftJoin(UserSettings::tableName(). ' us2',
                        "us2.setting_config_id = s2.id AND us2.user_id = u.id"  
                    )
                    ->where(count($status) ==0 ? [] : ['u.status' => $status])
                    ->groupBy(["setting_value", "setting2_value"])
                    ->orderBy("cnt DESC")
                    ->asArray()->all();
            
            $setting_column = [];
            $setting2_column = [];
            $arr = [];
            foreach ($data as $row) {
                $setting_column[$row['setting_value']] = $row['setting_value'];
                $setting2_column[$row['setting2_value']] = $row['setting2_value'];
                $arr[$row['setting_value']][$row['setting2_value']] = $row['cnt'];
            }
            
            $data = $arr;
        }

        return $this->render('index', [
            'data' => $data,
            'setting' => $setting,
            'setting2' => $setting2,
            'settings' => $settings,
            'setting_column' => $setting_column ?? [],
            'setting2_column' => $setting2_column ?? [],
            'status' => $status
        ]);
    }
}
