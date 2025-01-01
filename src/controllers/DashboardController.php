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
    public function actionIndex($setting = null)
    {
        if (!Yii::$app->user->identity->hasRole('admin')) {
            throw new NotFoundHttpException(Module::t('The requested page does not exist.'));
        }
        
        $settings = ArrayHelper::map(UserSettingsConfig::find()->all(), 'code', 'title');

        $setting_name = (!$setting || !isset($settings[$setting])) ? [array_key_first($settings)] : $setting;
        
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
                    ->groupBy(["setting_value"])
                    ->orderBy("cnt DESC")
                    ->asArray()->all();

        return $this->render('index', [
            'data' => $data,
            'setting' => $setting,
            'settings' => $settings
        ]);
    }
}
