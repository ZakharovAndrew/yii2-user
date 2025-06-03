<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\models\UserSettings;
use ZakharovAndrew\user\models\UserSettingsConfig;
use ZakharovAndrew\user\models\Roles;
use ZakharovAndrew\user\controllers\ParentController;

class WallpaperController extends ParentController
{
    public function actionIndex()
    {
        $wallpapers = Yii::$app->getModule('user')->wallpapers;
        $availableWallpapers = [];

        // Getting the roles of the current user
        $userRoles = ArrayHelper::getColumn(Roles::getRolesByUserId(Yii::$app->user->id), 'code');
        
        foreach ($wallpapers as $id => $wallpaper) {
            // Checking if the wallpaper is available for the user.
            if (array_intersect($userRoles, $wallpaper['roles'])) {
                $availableWallpapers[$id] = $wallpaper['url'];
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
        
        UserSettings::saveValue($userId, $settingConfig->id, $wallpaperId);
        
        Yii::$app->cache->delete('user_wallpaper_'.$userId);

        // Redirecting back to the wallpaper selection page
        return $this->redirect(['index']);
    }
}
