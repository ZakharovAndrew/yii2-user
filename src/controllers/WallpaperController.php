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
use ZakharovAndrew\user\models\Wallpaper;
use ZakharovAndrew\user\controllers\ParentController;

class WallpaperController extends ParentController
{
    /**
     * Display available wallpapers for user selection
     */
    public function actionIndex()
    {
        $wallpapers = Wallpaper::getAvailableWallpapersForUser(Yii::$app->user->identity);

        // Getting the setting ID by the code 'user_wallpaper_id'.
        $settingConfig = UserSettingsConfig::findOne(['code' => 'user_wallpaper_id']);
        if ($settingConfig === null) {
            throw new NotFoundHttpException('Setting not found.');
        }

        return $this->render('index', [
            'wallpapers' => $wallpapers,
            'currentWallpaperId' => $settingConfig->getUserSettingValue(Yii::$app->user->id) ?? 0,
        ]);
    }

    /**
     * Select wallpaper for current user
     */
    public function actionSelect($wallpaperId)
    {
        $userId = Yii::$app->user->id;
        
        // Verify wallpaper exists and is available for user
        $wallpaper = Wallpaper::findOne([
            'id' => $wallpaperId,
            'status' => Wallpaper::STATUS_ACTIVE
        ]);
        
        if (!$wallpaper) {
            throw new NotFoundHttpException('Wallpaper not found.');
        }
        
        // Check if wallpaper is available for user roles
        $userRoles = ArrayHelper::getColumn(Roles::getRolesByUserId($userId), 'code');
        $isAvailable = false;
        foreach ($userRoles as $role) {
            if ($wallpaper->isAvailableForRole($role)) {
                $isAvailable = true;
                break;
            }
        }
        
        if (!$isAvailable) {
            throw new NotFoundHttpException('Wallpaper not available for your role.');
        }

        // Getting the setting ID by the code 'user_wallpaper_id'.
        $settingConfig = UserSettingsConfig::findOne(['code' => 'user_wallpaper_id']);
        if ($settingConfig === null) {
            throw new NotFoundHttpException('Setting not found.');
        }
        
        UserSettings::saveValue($userId, $settingConfig->id, $wallpaperId);
        
        Yii::$app->cache->delete('user_wallpaper_'.$userId);

        // Redirecting back to the wallpaper selection page
        return $this->redirect(['index']);
    }
}
