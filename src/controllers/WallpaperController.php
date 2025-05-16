<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\models\UserSettings;
use ZakharovAndrew\user\models\UserSettingsConfig;

class WallpaperController extends Controller
{
    public function actionIndex()
    {
        $wallpapers = Yii::$app->getModule('user')->wallpapers;
        $availableWallpapers = [];

        // Getting the roles of the current user
        $userRoles = User::getRolesByUserId(Yii::$app->user->id);

        foreach ($wallpapers as $wallpaper) {
            // Checking if the wallpaper is available for the user.
            if (array_intersect($userRoles, $wallpaper['roles'])) {
                $availableWallpapers[] = $wallpaper['url'];
            }
        }

        // Getting the setting ID by the code 'user_wallpaper_id'.
        $settingConfig = UserSettingsConfig::findOne(['code' => 'user_wallpaper_id']);
        if ($settingConfig === null) {
            throw new NotFoundHttpException('Настройка не найдена.');
        }

        return $this->render('index', [
            'wallpapers' => $availableWallpapers,
            'currentWallpaperId' => $settingConfig->getUserSettingValue(Yii::$app->user->id) ?? 0,
        ]);
    }

    public function actionSelect($wallpaperId)
    {
        $userId = Yii::$app->user->id;
        
        // Getting the setting ID by the code 'user_wallpaper_id'.
        $settingConfig = UserSettingsConfig::findOne(['code' => 'user_wallpaper_id']);
        if ($settingConfig === null) {
            throw new NotFoundHttpException('Настройка не найдена.');
        }
        
        UserSettings::saveValue($userId, $settingConfigId, $wallpaperId);

        // Redirecting back to the wallpaper selection page
        return $this->redirect(['index']);
    }
}
