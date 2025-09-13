<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use ZakharovAndrew\user\models\Vacation;
use ZakharovAndrew\user\models\VacationSearch;
use ZakharovAndrew\user\models\VacationType;
use ZakharovAndrew\user\models\VacationRequestForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use ZakharovAndrew\user\Module;

/**
 * VacationController implements the CRUD actions for Vacation model.
 */
class VacationController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Только авторизованные пользователи
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Vacation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VacationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Vacation model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Vacation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Vacation();
        $model->user_id = Yii::$app->user->id; // Текущий пользователь

        if ($model->load(Yii::$app->request->post())) {
            // Автоматически рассчитываем количество дней
            $model->days_count = $model->calculateDaysCount();
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Module::t('Vacation requested successfully'));
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', Module::t('Error creating vacation request'));
            }
        }

        return $this->render('create', [
            'model' => $model,
            'vacationTypes' => VacationType::getTypesList(),
        ]);
    }

    /**
     * Updates an existing Vacation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Проверяем, может ли пользователь редактировать этот отпуск
        if (!$model->canBeEdited()) {
            Yii::$app->session->setFlash('error', Module::t('You cannot edit this vacation'));
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->days_count = $model->calculateDaysCount();
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Module::t('Vacation updated successfully'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'vacationTypes' => VacationType::getTypesList(),
        ]);
    }

    /**
     * Deletes an existing Vacation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // Проверяем, может ли пользователь удалить этот отпуск
        if (!$model->canBeCancelled()) {
            Yii::$app->session->setFlash('error', Module::t('You cannot delete this vacation'));
            return $this->redirect(['index']);
        }

        if ($model->cancel()) {
            Yii::$app->session->setFlash('success', Module::t('Vacation cancelled successfully'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Error cancelling vacation'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Approve vacation action (for managers/admins)
     * @param integer $id
     * @return mixed
     */
    public function actionApprove($id)
    {
        $model = $this->findModel($id);

        if ($model->approve(Yii::$app->user->id)) {
            Yii::$app->session->setFlash('success', Module::t('Vacation approved successfully'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Error approving vacation'));
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Reject vacation action (for managers/admins)
     * @param integer $id
     * @return mixed
     */
    public function actionReject($id)
    {
        $model = $this->findModel($id);
        $comment = Yii::$app->request->post('comment');

        if ($model->reject(Yii::$app->user->id, $comment)) {
            Yii::$app->session->setFlash('success', Module::t('Vacation rejected successfully'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Error rejecting vacation'));
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * User's own vacations
     * @return mixed
     */
    public function actionMy()
    {
        $searchModel = new VacationSearch();
        $searchModel->user_id = Yii::$app->user->id;
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('my', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Team vacations (for managers)
     * @return mixed
     */
    public function actionTeam()
    {
        // Здесь должна быть логика получения отпусков команды пользователя
        // Например, подчиненных или всей команды
        
        $searchModel = new VacationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('team', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Calendar view of vacations
     * @return mixed
     */
    public function actionCalendar($userId = null, $typeId = null)
    {
        $vacations = Vacation::getForCalendar($userId, $typeId);
    
        return $this->render('calendar', [
            'vacations' => $vacations,
            'userId' => $userId,
            'typeId' => $typeId,
        ]);
    }
    
    /**
     * Approve or reject vacation request
     * @param integer $id Vacation ID
     * @return mixed
     */
    public function actionProcessRequest($id)
    {
        $vacation = $this->findModel($id);

        // Проверяем права
        if (!$this->canProcessRequest($vacation)) {
            Yii::$app->session->setFlash('error', Module::t('You cannot process this vacation request'));
            return $this->redirect(['view', 'id' => $id]);
        }

        $model = new VacationRequestForm();
        $model->vacation_id = $id;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->process()) {
                return $this->redirect(['view', 'id' => $id]);
            }
        }

        return $this->render('process-request', [
            'model' => $model,
            'vacation' => $vacation,
        ]);
    }

    /**
     * Check if user can process this vacation request
     */
    protected function canProcessRequest($vacation)
    {
        // Админы могут обрабатывать все запросы
        if (Yii::$app->user->identity->isAdmin()) {
            return true;
        }
        
        return false;

        // Руководитель может обрабатывать запросы своих подчиненных
        /*$manager = Yii::$app->user->identity;
        $subordinates = $manager->getAllSubordinates();
        $subordinateIds = ArrayHelper::getColumn($subordinates, 'id');

        return in_array($vacation->user_id, $subordinateIds);*/
    }

    /**
     * Finds the Vacation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Vacation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Vacation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('The requested page does not exist.'));
    }
}