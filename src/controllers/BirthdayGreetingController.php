<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use ZakharovAndrew\user\models\BirthdayGreeting;
use ZakharovAndrew\user\models\BirthdayGreetingSearch;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\Module;

class BirthdayGreetingController extends Controller
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
    public function actionIndex()
    {
        if (!Yii::$app->user->identity->hasRole('admin')) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        $searchModel = new BirthdayGreetingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSend($id)
    {
        $model = new BirthdayGreeting();

        $user = $this->findUser($id);
        
        // User can't congratulate yourself
        if ($user->id == Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', Module::t("You can't congratulate yourself"));
            
            return $this->redirect(['/user/user/profile', 'id' => $user->id]);
        }
        
        // Check if today is Monday
        $isMonday = (date('N') == 1); // 1 means Monday
        
        // Calculate the dates for the previous Saturday and Sunday
        $saturday = date('m-d', strtotime('last Saturday')); // Get last Saturday's date
        $sunday = date('m-d', strtotime('last Sunday')); // Get last Sunday's date
        
        if (!$user->isBirthdayToday() && !$isMonday && !in_array(date('m-d', strtotime($user->birthday)), [$saturday, $sunday])) {
            Yii::$app->session->setFlash('error', Module::t("The user's birthday is not today"));
            
            return $this->redirect(['/user/user/profile', 'id' => $user->id]);
        }
        
        
        if ($model->load(Yii::$app->request->post())) {
            $model->author_id = Yii::$app->user->id;
            $model->user_id = $id;
            
            if ($model->save()) {
                $model->sendEmail();
                Yii::$app->session->setFlash('success', Module::t('Congratulations sent'));
                return $this->redirect(['view', 'id' => $model->user_id]);
            } else {
                Yii::$app->session->setFlash('error', Module::t('Error saving greetings'));
            }
        }

        return $this->render('send', ['model' => $model]);
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
        if (($model = Thanks::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
