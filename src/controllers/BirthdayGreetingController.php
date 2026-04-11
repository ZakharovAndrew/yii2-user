<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use ZakharovAndrew\user\models\BirthdayGreeting;
use ZakharovAndrew\user\models\BirthdayGreetingSearch;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\controllers\ParentController;

class BirthdayGreetingController extends ParentController
{
    public $auth_access_actions = ['index', 'send', 'view'];
    
    public $action_allowed_roles = ['index' => ['admin']];
    
    /**
     * Lists all Thanks models.
     *
     * @return string
     */
    public function actionIndex()
    {        
        $searchModel = new BirthdayGreetingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Check if user can send birthday greeting
     * 
     * @param User $user The user to congratulate
     * @return bool Whether greeting can be sent
     */
    private function canSendGreeting($user)
    {        
        // User can't congratulate yourself
        if ($user->id == Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', Module::t("You can't congratulate yourself"));
            return false;
        }
        
        // Check if user has birthday today
        if ($user->isBirthdayToday()) {
            return true;
        }
        
        // Allow sending on Monday for birthdays that occurred on weekend
        $isMonday = (date('N') == 1);
        if ($isMonday) {
            $saturday = date('m-d', strtotime('last Saturday')); // Get last Saturday's date
            $sunday = date('m-d', strtotime('last Sunday')); // Get last Sunday's date
            
            if (in_array(date('m-d', strtotime($user->birthday)), [$saturday, $sunday])) {
                return true;
            }
        }
        
        Yii::$app->session->setFlash('error', Module::t("The user's birthday is not today"));
        return false;
    }

    public function actionSend($id)
    {
        $user = $this->findUser($id);
        
        if (!$this->canSendGreeting($user)) {
            return $this->redirect(['/user/user/profile', 'id' => $id]);
        }
        
        $model = new BirthdayGreeting();        
        
        if ($model->load(Yii::$app->request->post())) {
            $model->author_id = Yii::$app->user->id;
            $model->user_id = $user->id;
            
            if ($model->save()) {
                $model->sendEmail();
                Yii::$app->session->setFlash('success', Module::t('Congratulations sent'));
                return $this->redirect(['view', 'id' => $model->user_id]);
            } else {
                Yii::$app->session->setFlash('error', Module::t('Error saving greetings'));
            }
        }
        
        return $this->render('send', [
            'model' => $model,
            'user' => $user
        ]);
    }

    public function actionView($id = null)
    {
        if (is_null($id)) {
            $id = Yii::$app->user->id;
        }
        
        $user = $this->findUser($id);
        
        $searchModel = new BirthdayGreetingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

        return $this->render('view', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user' => $user
        ]);
    }
    
    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findUser($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findModel($id)
    {
        if (($model = BirthdayGreeting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
