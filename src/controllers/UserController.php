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
use ZakharovAndrew\user\models\UserSettings;
use ZakharovAndrew\user\models\UserSettingsConfig;
use ZakharovAndrew\user\models\LoginAttempt;
use yii\helpers\Url;
// for avatar uploading
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for User model.
 * @author Andrew Zakharov https://github.com/ZakharovAndrew
 */
class UserController extends ParentController
{
    public $controller_id = 1001;
    
    public $full_access_actions = ['login', 'logout', 'request-password-reset', 'reset-password', 'set-new-email', 'change-password', 'telegram-registration', 'signup'];

    public $auth_access_actions = ['profile', 'edit-profile', 'upload-avatar', 'delete-avatar'];
            
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
        
        $settings = UserSettingsConfig::find()->where([
            'access_level' => [UserSettingsConfig::CHANGE_ADMIN_ONLY, UserSettingsConfig::CHANGE_USER_AND_ADMIN]
        ])->all();

        if ($this->request->isPost && $model->load($this->request->post())) {
            // generate a random password
            $password = User::genPassword();
            $model->generateTelegramCode(); // set telegram code
            $model->created_by = Yii::$app->user->id;
            
            // Trying to send the password to the email and save the password
            if (!$model->sendPasswordEmail($password) || !$model->setPassword($password)) {
                Yii::$app->session->setFlash('error', Module::t('Error creating user. Error setting password.'));
                return $this->redirect(Url::previous('user_index') ?? ['index']);
            }
            
            if (!$model->save()) {
                Yii::$app->session->setFlash('error', Module::t('Error. Failed to save user during creation'));
            } else {
                // save user settings
                foreach ($settings as $setting) {
                    $value = Yii::$app->request->post($setting->code) ?? null;
                    UserSettings::saveValue($model->id, $setting->id, $value);
                }

                Yii::$app->session->setFlash('success', Module::t('User created'));
            }
            
            return $this->redirect(Url::previous('user_index') ?? ['index']);
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'settings' => $settings,
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
    
    public function actionAdminResetPassword($id)
    {
        $user = $this->findModel($id);
        
        $password = User::genPassword();
        
        if ($user->setPassword($password) && $user->save() && $user->sendPasswordEmail($password, 'reset')) {
            Yii::$app->session->setFlash('success', Module::t('A new password has been set and sent to') .' '. $user->email);
            return $this->redirect(Url::previous('user_index') ?? ['index']);
        } else {
            Yii::$app->session->setFlash('error', Module::t('There was an error during the password reset'));
            return $this->redirect(Url::previous('adm_user_index') ?? ['adm-user/index']);
        }
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
                    Yii::$app->session->setFlash('error', Module::t('Error changing password.'));
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
        
        // Get the user's IP address
        $userIp = Yii::$app->request->userIP;

        // Check if the IP address is blocked
        if (LoginAttempt::isBlockedByIp($userIp)) {
            Yii::$app->session->setFlash('error', Module::t('Too many unsuccessful attempts. Please wait an hour before trying again.'));
            return $this->render('login', ['model' => $model]);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->login()) {
                // Successful authentication
                LoginAttempt::logLoginAttempt($model->username, $userIp, true);
                return $this->goBack();
            } else {
                // Unsuccessful authentication
                LoginAttempt::logLoginAttempt($model->username, $userIp, false);
                Yii::$app->session->setFlash('error', Module::t('Incorrect username or password.' ));
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }
    
    /**
     * Register action.
     *
     * @return Response|string
     */
    public function actionSignup()
    {
        $this->layout = 'login';
        
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        if (!Yii::$app->getModule('user')->enableUserSignup) {
            return $this->render('signup_disabled');
        }
        
        $model = new \ZakharovAndrew\user\models\SignupForm();
        
        return $this->render('signup', [
            'model' => $model,
        ]);
    }
        
    /**
     * User profile
     * 
     * @param int $id User ID 
     * @return mixed
     */
    public function actionProfile($id = null)
    {        
        // if the current user's profile
        if (empty($id)) {
            $model = Yii::$app->user->identity;
        } else {
            $model = $this->findModel($id);
        }
        
        // if the code is empty
        if (empty($model->telegram_id)) {
            $model->telegram_code = md5(time().$model->id);
            $model->save();
        }
        
        $settings = UserSettingsConfig::find()->where([
            'access_level' => [
                UserSettingsConfig::CHANGE_USER_AND_ADMIN,
                UserSettingsConfig::CHANGE_USER_ONLY,
                UserSettingsConfig::CHANGE_ADMIN_ONLY
            ]
        ])->all();
        
        return $this->render('profile', [
            'model' => $model,
            'settings' => $settings
        ]);
    }
    
    public function actionEditProfile($id = null)
    {        
        // if the current user's profile
        if (empty($id)) {
            $model = Yii::$app->user->identity;
        } else {
            if (!Yii::$app->user->identity->hasRole('admin')) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            $model = $this->findModel($id);
        }
        
        $settings = UserSettingsConfig::find()->where([
            'access_level' => Yii::$app->user->identity->hasRole('admin') ? [UserSettingsConfig::CHANGE_USER_AND_ADMIN, 3] : [UserSettingsConfig::CHANGE_USER_AND_ADMIN, 2]
        ])->all();
        
        if ($this->request->isPost && $model->load($this->request->post()) && $model->validate()) {
            if ($model->save()) {
                // save user settings
                foreach ($settings as $setting) {
                    $value = Yii::$app->request->post($setting->code) ?? null;
                    UserSettings::saveValue($model->id, $setting->id, $value);
                }

                Yii::$app->session->setFlash('success', Module::t('Profile updated'));
            } else {
                Yii::$app->session->setFlash('error', Module::t('Profile update error'));
            }
        }
        
        return $this->render('editProfile', [
            'model' => $model,
            'settings' => $settings
        ]);
    }
    
    public function actionTelegramRegistration($user_id = null, $code = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;

        if (empty($user_id) && empty($code)) {
            echo Module::t('Wrong code');
            return;
        }
        
        if (($model = User::findOne(['telegram_id' => $user_id])) !== null) {
            echo Module::t('You are already registered');
            return;
        }
        
        if (($model = User::findOne(['telegram_code' => $code])) === null) {
            echo Module::t('No such code exists');
            return;
        }
        
        $model->telegram_id = $user_id;
        $model->save();
        
        echo 'Вы успешно зарегистрированы';
    }
    
    // Unlink user account to the telegram bot
    public function actionUnlinkTelegram()
    {   
        $user = Yii::$app->user->identity;
        $user->telegram_id = null;
        $user->save();
        
        Yii::$app->session->setFlash('success', Module::t('Successfully unlinked your account from the telegram bot'));
        
        return $this->redirect(['/user/user/profile']);
    }
    
    public function actionUploadAvatar()
    {
        $user = Yii::$app->user->identity;

        if ($user->load(Yii::$app->request->post())) {
            $user->avatar = UploadedFile::getInstance($user, 'avatar');

            if ($user->avatar instanceof yii\web\UploadedFile && $user->uploadAvatar()) {
                Yii::$app->session->setFlash('success', 'Avatar uploaded successfully');
                return $this->redirect(['/user/user/profile']);
            } else {
                Yii::$app->session->setFlash('error', 'Error uploading avatar');
            }
        }

        return $this->render('upload-avatar', ['model' => $user]);
    }
    
    public function actionDeleteAvatar()
    {
        $user = Yii::$app->user->identity;
        $user->avatar = null;
        $user->save();
        
        Yii::$app->session->setFlash('success', Module::t('Avatar deleted successfully'));
        
        return $this->redirect(['/user/user/profile']);
    }
    
    public function actionPasteRoles()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        if ($this->request->isPost) {
            $from = Yii::$app->request->post('from');
            $to = Yii::$app->request->post('to');
            
            $fromUsers = User::findOne(['id' => $from]);
            $toUsers = User::findOne(['id' => $to]);
            
            if (!$fromUsers || !$toUsers) {
                return ['result' => 'error', 'massage' => 'Wrong users!'];
            }
            
            $rolesToCopy = \ZakharovAndrew\user\models\UserRoles::find()->where(['user_id' => $fromUsers->id])->all();
            
            foreach($rolesToCopy as $role) {
                $userRole = new \ZakharovAndrew\user\models\UserRoles([
                    'user_id' => $toUsers->id,
                    'role_id' => $role->role_id,
                    'note' => $role->note,
                    'subject_id' => $role->subject_id,
                ]);
                
                $userRole->save();
                unset($userRole);
            }
            
            return ['result' => 'ok'];
        }
        
        return ['result' => 'error'];
    }
    
    
    public function actionUsersUpdate()
    {
        if (!Yii::$app->user->identity->hasRole('admin')) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        $selectedUserIds = Yii::$app->request->post('selection'); // retrieve selected user IDs
        $status = Yii::$app->request->post('status'); // retrieve selected status
        $role = Yii::$app->request->post('role'); // retrieve selected roles
        $action = Yii::$app->request->post('form-action');

        $users = User::find()->where(['id' => $selectedUserIds])->all();
        
        if ($action == 'Add Role') {
            //Subject of the role
            $subject_id = Yii::$app->request->post('subject_id') ?? null;
            foreach ($users as $user) {
                $model = new \ZakharovAndrew\user\models\UserRoles([
                    'role_id' => $role,
                    'user_id' => $user->id,
                    'subject_id' => $subject_id
                ]);
                $model->save();
                unset($model);
            }
            Yii::$app->session->setFlash('success', Module::t('Roles added'));
        } else if ($action == 'Update Status') {
            foreach ($users as $user) {
                $user->status = $status;
                $user->save();
            }
            Yii::$app->session->setFlash('success', Module::t('Statuses changed'));
        } else if ($action == 'Delete users') {
            foreach ($users as $user) {
                $user->delete();
            }
            Yii::$app->session->setFlash('success', Module::t('Users deleted'));
        } else {
            $cntSuccess = 0;
            $cntError = 0;
            foreach ($users as $user) {
                $password = User::genPassword();
        
                if ($user->setPassword($password) && $user->save() && $user->sendPasswordEmail($password, 'reset')) {
                    $cntSuccess++;
                } else {
                    $cntError++;
                }
            }
            
            Yii::$app->session->setFlash('info', Module::t('Successfully reset passwords').': <b>'.$cntSuccess .'</b><br> '.Module::t('Password reset errors').': '.$cntError);
        }
        
        
        // redirect back to the GridView
        return $this->redirect(Url::previous('user_index') ?? ['index']);
    }
    
    public function actionToggleColumnVisibility()
    {
        $column = Yii::$app->request->post('column');
        $visibility = Yii::$app->request->post('visibility', true);
        
        $columnVisibility = \ZakharovAndrew\user\models\User::getColumnVisibility();
        
        if (isset($column)) {
            $columnVisibility[$column] = ($visibility == "true");
            Yii::$app->session->set('gridViewColumnVisibility', $columnVisibility);
        }
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
