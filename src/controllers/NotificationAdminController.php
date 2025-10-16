<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use ZakharovAndrew\user\models\NotificationGroup;
use ZakharovAndrew\user\models\Notification;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class NotificationAdminController extends ParentController
{
    public $controller_id = 1009;
    
    public function actionIndex()
    {
        $groups = NotificationGroup::find()->all(); // Fetch all notification groups
        
        return $this->render('index', [
            'groups' => $groups,
            'model' => new NotificationGroup(),
            'modelNotification' => new Notification()
        ]);
    }
    
    // Action to handle AJAX request for creating a new NotificationGroup
    public function actionCreateGroupAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON; // Set response format to JSON
        
        $model = new NotificationGroup();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ['success' => true, 'group' => $model]; // Return success response with the new group
        }

        return ['success' => false, 'errors' => $model->getErrors()]; // Return error response if saving fails
    }

    public function actionDeleteGroup($id)
    {
        $model = NotificationGroup::findOne($id);

        if ($model) {
            $model->delete();
            Yii::$app->session->setFlash('success', 'Group deleted successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Group not found.');
        }

        return $this->redirect(['index']); // Перенаправление обратно на страницу с группами
    }
    

    /**
     * Сreating a notification
     * @param integer $groupId - ID of the group to which the notification is added
     * @return type
     */
    public function actionCreateNotificationAjax($groupId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $group = $this->findGroup($groupId);
        $model = new Notification();

        if ($model->load(Yii::$app->request->post())) {
            $model->notification_group_id = $group->id;
            if ($model->save()) {
                return ['success' => true, 'notification' => $model];
            }
        } else {
            echo 'asd';
        }

        return ['success' => false, 'errors' => $model->getErrors()];
    }
    
    public function actionEditNotificationAjax($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Notification::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'Notification not found'];
        }
        
        if ($model->load(Yii::$app->request->post())) {
            // Save roles
            $roleIds = Yii::$app->request->post('Notification')['roles'] ?? [];
            $model->setRoles($roleIds);

            if ($model->save()) {
                // Convert roles to an array for returning in JSON
                $roles = array_map(function ($role) {
                    return $role->id;
                }, $model->roles);
                
                return [
                    'success' => true,
                    'notification' => [
                        'id' => $model->id,
                        'name' => $model->name,
                        'description' => $model->description,
                        'code_name' => $model->code_name,
                        'function_to_call' => $model->function_to_call,
                        'roles' => $roles,
                    ],
                ];
            }
        }

        return ['success' => false, 'errors' => $model->getErrors()];
    }
    
    public function actionDeleteNotificationAjax($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Notification::findOne($id);
        if ($model) {
            $model->delete();
            return ['success' => true];
        }

        return ['success' => false, 'message' => 'Notification not found'];
    }

    public function actionEditGroupAjax($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findGroup($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ['success' => true, 'group' => $model];
        }

        return ['success' => false, 'errors' => $model->getErrors()];
    }


    // Helper method to find a NotificationGroup by ID
    protected function findGroup($id)
    {
        if (($model = NotificationGroup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.'); // Throw an error if the group does not exist
    }
}
