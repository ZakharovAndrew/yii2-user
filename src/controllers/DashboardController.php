<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use ZakharovAndrew\user\models\Thanks;
use ZakharovAndrew\user\models\ThanksSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\UserSettings;
use ZakharovAndrew\user\models\UserSettingsConfig;
use ZakharovAndrew\user\models\User;
use \yii\helpers\ArrayHelper;

class DashboardController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Thanks models.
     *
     * @return string
     */
    public function actionIndex($filter = null)
    {
        if (!Yii::$app->user->identity->hasRole('admin')) {
            throw new NotFoundHttpException(Module::t('The requested page does not exist.'));
        }
        
        $settings = ArrayHelper::map(UserSettingsConfig::find()->all(), 'code', 'title');

        $setting_name = (!$filter || !isset($settings[$filter])) ? [array_key_first($settings)] : $filter;
        
        $setting_i = 1;
        $data = User::find()->alias('u')
                    ->select(['cnt' => 'count(*)', "us{$setting_i}.values"])
                    ->leftJoin(UserSettingsConfig::tableName(). ' s'.$setting_i,
                        ['s'.$setting_i.".code" => $setting_name]
                    )
                    ->leftJoin(UserSettings::tableName(). ' us'.$setting_i,
                        "us{$setting_i}.setting_config_id = s{$setting_i}.id AND us{$setting_i}.user_id = u.id"  
                    )
                    ->groupBy(["us{$setting_i}.values"])
                    ->orderBy("cnt DESC")
                    ->asArray()->all();

        return $this->render('index', [
            'data' => $data,
            'settings' => $settings
        ]);
    }
}
