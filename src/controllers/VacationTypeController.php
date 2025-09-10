<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use ZakharovAndrew\user\models\VacationType;
use ZakharovAndrew\user\models\VacationTypeSearch;
use yii\web\NotFoundHttpException;
use ZakharovAndrew\user\Module;


/**
 * VacationTypeController implements the CRUD actions for VacationType model.
 */
class VacationTypeController extends ParentController
{
    public $controller_id = 1007;

    /**
     * Lists all VacationType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VacationTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single VacationType model.
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
     * Creates a new VacationType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new VacationType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Module::t('Vacation type created successfully'));
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing VacationType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Module::t('Vacation type updated successfully'));
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing VacationType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Проверяем, используется ли тип отпуска
        if ($model->getVacations()->exists()) {
            Yii::$app->session->setFlash('error', Module::t('Cannot delete vacation type that is in use'));
            return $this->redirect(['index']);
        }
        
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', Module::t('Vacation type deleted successfully'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Error deleting vacation type'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Toggle active status of vacation type
     * @param integer $id
     * @return mixed
     */
    public function actionToggleActive($id)
    {
        $model = $this->findModel($id);
        $model->is_active = !$model->is_active;
        
        if ($model->save()) {
            Yii::$app->session->setFlash('success', Module::t('Vacation type status updated'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Error updating vacation type status'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the VacationType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VacationType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VacationType::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('The requested page does not exist.'));
    }
}