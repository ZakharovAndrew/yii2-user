<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use yii\web\Controller;
use ZakharovAndrew\user\models\NotificationGroup;
use ZakharovAndrew\user\models\Notification;
use ZakharovAndrew\user\models\UserNotificationSetting;
use ZakharovAndrew\user\models\UserRoles;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class NotificationsController extends Controller
{
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;

        $userRoleIds = UserRoles::getUserRolesIds($userId);      

        // Запрос для получения всех групп уведомлений и связанных с ними уведомлений
        $groups = NotificationGroup::find()
            ->with([
                'notifications' => function ($query) use ($userRoleIds) {
                    // Фильтруем уведомления:
                    // - Если уведомление связано с ролями, проверяем, есть ли совпадение с ролями пользователя.
                    // - Если уведомление НЕ связано с ролями, оно доступно всем.
                    $query->joinWith('roles')
                        ->andWhere([
                            'or',
                            ['notification_role.role_id' => null], // Уведомления без ролей (доступны всем)
                            ['and', ['notification_role.role_id' => $userRoleIds]], // Уведомления с ролями, соответствующими пользователю
                        ])
                        ->groupBy('notifications.id'); // Группируем по ID уведомлений, чтобы избежать дубликатов
                },
            ])
            ->all();

        return $this->render('index', [
            'groups' => $groups,
            'userId' => $userId,
        ]);
    }
    
    public function actionSaveNotificationSetting()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $userId = Yii::$app->request->post('user_id');
        $notificationId = Yii::$app->request->post('notification_id');
        $type = Yii::$app->request->post('type');
        $value = Yii::$app->request->post('value');

        $setting = UserNotificationSetting::findOne([
            'user_id' => $userId,
            'notification_id' => $notificationId,
        ]);

        if (!$setting) {
            $setting = new UserNotificationSetting([
                'user_id' => $userId,
                'notification_id' => $notificationId,
            ]);
        }
        
        $setting->{'send_' . $type} = ($value=="false" ? 0 : 1);
        
        if ($setting->save()) {
            return ['success' => true, 'res' => $value];
        }

        return ['success' => false, 'errors' => $setting->getErrors()];
    }
}
