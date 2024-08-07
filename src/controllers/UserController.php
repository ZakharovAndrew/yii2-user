<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use app\models\User;
use ZakharovAndrew\user\models\UserSearch;
use ZakharovAndrew\user\models\ChangeEmailForm;
use ZakharovAndrew\user\controllers\ParentController;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\base\InvalidParamException;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\ResetPasswordForm;
use ZakharovAndrew\user\models\PasswordResetRequestForm;
use ZakharovAndrew\user\models\ChangePasswordForm;
use yii\helperers\Url;

/**
 * UserController implements the CRUD actions for User model.
 * @author Andrew Zakharov https://github.com/ZakharovAndrew
 */
class UserController extends ParentController
{
    public $controller_id = 1001;
    
    public $full_access_actions = ['login', 'logout', 'request-password-reset', 'reset-password', 'set-new-email', 'change-password'];

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        
        Url::remember('', 'user_index');

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
                return $this->redirect(Url::previous('user_index') ?? ['index']);
            }
            
            if (!$model->save()) {
                Yii::$app->session->setFlash('error', Module::t('Error. Failed to save user during creation'));
            }
            
            return $this->redirect(Url::previous('user_index') ?? ['index']);
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
            return $this->redirect(Url::previous('user_index') ?? ['index']);
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

        return $this->redirect(Url::previous('user_index') ?? ['index']);
    }
    
    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        // not guest cannot reset password
        if (!Yii::$app->user->isGuest) {
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
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', Module::t('New password was saved.'));
            return $this->goHome();
        }

        $this->layout = "login";

        return $this->render('resetPasswordForm', [
            'model' => $model,
            'token' => $token,
        ]);
    }
    
    /**
     * Change email
     * 
     * @return mixed
     */
    public function actionChangeEmail()
    {
        $this->layout = 'login';
        
        $user = Yii::$app->user->identity;
        $model = new ChangeEmailForm();
        
        if ($this->request->isPost) {
            if (!$model->load($this->request->post()) && !$model->validate()) {
                Yii::$app->session->setFlash('error', Module::t('Invalid data for changing Email.'));
                return $this->render('changeEmail', ['model' => $model]);
            }
            
            if (!$user->validatePassword($model->password)) {
                Yii::$app->session->setFlash('error', Module::t('Wrong password.'));
                return $this->render('changeEmail', ['model' => $model]);
            }
            
            if ($model->sendEmail($user)){
                Yii::$app->session->setFlash('success', Module::t('We have sent a link to confirm your new email address. Please follow it to confirm.'));
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', Module::t('Error when changing email'));
            }   
        }
        
        return $this->render('changeEmail', [
            'model' => $model,
        ]);
    }
    
    public function actionSetNewEmail($username, $email, $key)
    {
        if (($user = User::findOne(['id' => Yii::$app->user->id, 'username' => $username])) === null) {
            Yii::$app->session->setFlash('error', Module::t('You are not authorized to change Email'));
            return $this->redirect(['/user/user/login']);
        }

        if (md5($email.Yii::$app->name) != $key) {
            Yii::$app->session->setFlash('error', Module::t('Error when changing email, email not confirmed'));
            return $this->redirect(['/user/user/change-email']);
        }

        $user->email = $email;

        if(!$user->save()) {
            Yii::$app->session->setFlash('error', Module::t('Error when changing email') . ': ' . (isset($user->errors['email'])) ? $user->errors['email'][0] : var_export($user->errors, true));
            return $this->redirect(['/user/change-email']);
        }

        Yii::$app->session->setFlash('success', Module::t('Email changed successfully'));
        return $this->goHome();
    }
    
    /**
     * Change user password
     * 
     * @return mixed
     */
    public function actionChangePassword()
    {
        // guest cannot change password
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $this->layout = 'login';
        
        $model = new ChangePasswordForm();
        $user = Yii::$app->user->identity;
        
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->validate()) {
                if ($user->setPassword($model->new_password) && $user->save(false)){
                    Yii::$app->session->setFlash('success', Module::t('Your password has been successfully changed.'));
                    return $this->goHome();
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка при смене пароля.');
                }
            }
        }
        
        return $this->render('changePasswordForm', [
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
