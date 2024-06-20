<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use app\models\User;
use ZakharovAndrew\user\models\UserSearch;
use ZakharovAndrew\user\controllers\ParentController;
use yii\web\NotFoundHttpException;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\PasswordResetRequestForm;

/**
 * UserController implements the CRUD actions for User model.
 * @author Andrew Zakharov https://github.com/ZakharovAndrew
 */
class UserController extends ParentController
{
    public $controller_id = 1001;
    
    public $full_access_actions = ['login', 'logout'];

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new User();

        if ($this->request->isPost && $model->load($this->request->post())) {
            // generate a random password
            $password = User::genPassword();
            
            // Trying to send the password to the email and save the password
            if (!$model->sendPasswordEmail($password) || !$model->setPassword($password)) {
                Yii::$app->session->setFlash('error', Module::t('Error creating user. Error setting password.'));
                return $this->redirect(['index']);
            }
            
            if (!$model->save()) {
                Yii::$app->session->setFlash('error', Module::t('Error. Failed to save user during creation'));
            }
            
            return $this->redirect(['index']);
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        // guest cannot reset password
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Module::t('Check your email for further action'));
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', Module::t('An error occurred while resetting your password. Contact the site administrator'));
            }
        }

        $this->layout = "login";

        return $this->render('passwordResetRequestForm', [
            'model' => $model,
        ]);
    }
    
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = 'login';
        
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new \ZakharovAndrew\user\models\LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
