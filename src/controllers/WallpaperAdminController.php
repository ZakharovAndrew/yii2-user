<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\Wallpaper;
use ZakharovAndrew\user\models\Roles;
use ZakharovAndrew\user\models\UserSettings;
use ZakharovAndrew\user\models\UserSettingsConfig;
use ZakharovAndrew\user\controllers\ParentController;

class WallpaperAdminController extends ParentController
{
    /**
     * Admin panel - list all wallpapers
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Wallpaper::find()->orderBy(['position' => SORT_ASC, 'id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Create new wallpaper
     */
    public function actionCreate()
    {
        $model = new Wallpaper();

        if ($model->load(Yii::$app->request->post())) {
            // Handle image upload if needed
            $imageFile = UploadedFile::getInstance($model, 'image_file');
            if ($imageFile) {
                $model->image_url = $this->handleImageUpload($imageFile);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Module::t('Wallpaper created successfully.'));
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'allRoles' => ArrayHelper::map(Roles::find()->all(), 'code', 'title')
        ]);
    }

    /**
     * Update existing wallpaper
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            // Handle image upload if needed
            $imageFile = UploadedFile::getInstance($model, 'image_file');
            if ($imageFile) {
                $model->image_url = $this->handleImageUpload($imageFile);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Module::t('Wallpaper updated successfully.'));
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'allRoles' => ArrayHelper::map(Roles::find()->all(), 'code', 'title')
        ]);
    }

    /**
     * Delete wallpaper
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Check if this wallpaper is used by any user
        $settingConfig = UserSettingsConfig::findOne(['code' => 'user_wallpaper_id']);
        if ($settingConfig) {
            $usersWithThisWallpaper = UserSettings::find()
                ->where(['setting_id' => $settingConfig->id, 'value' => $id])
                ->count();
                
            if ($usersWithThisWallpaper > 0) {
                Yii::$app->session->setFlash('error', 'Cannot delete wallpaper. It is used by ' . $usersWithThisWallpaper . ' users.');
                return $this->redirect(['index']);
            }
        }
        
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', Module::t('Wallpaper deleted successfully.'));
        } else {
            Yii::$app->session->setFlash('error', 'Error deleting wallpaper.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Toggle wallpaper status
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status == Wallpaper::STATUS_ACTIVE 
            ? Wallpaper::STATUS_INACTIVE 
            : Wallpaper::STATUS_ACTIVE;
            
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Wallpaper status updated.');
        } else {
            Yii::$app->session->setFlash('error', 'Error updating wallpaper status.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Move wallpaper position up
     */
    public function actionMoveUp($id)
    {
        $model = $this->findModel($id);
        if ($model->moveUp()) {
            Yii::$app->session->setFlash('success', Module::t('Position updated successfully'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Cannot move wallpaper up'));
        }
        
        if (Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
        return $this->redirect(['index']);
    }
    
    /**
     * Move wallpaper position down
     */
    public function actionMoveDown($id)
    {
        $model = $this->findModel($id);
        if ($model->moveDown()) {
            Yii::$app->session->setFlash('success', Module::t('Position updated successfully'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Cannot move wallpaper down'));
        }
        
        if (Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
        return $this->redirect(['index']);
    }

    /**
     * Find wallpaper model
     */
    protected function findModel($id)
    {
        if (($model = Wallpaper::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested wallpaper does not exist.');
    }

    /**
     * Handle image upload
     */
    protected function handleImageUpload($imageFile)
    {
        $uploadPath = Yii::getAlias('@webroot/uploads/wallpapers');
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $fileName = uniqid() . '.' . $imageFile->extension;
        $filePath = $uploadPath . '/' . $fileName;

        if ($imageFile->saveAs($filePath)) {
            return Yii::getAlias('@web/uploads/wallpapers/' . $fileName);
        }

        return null;
    }

    /**
     * View wallpaper details
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        // Get usage statistics
        $settingConfig = UserSettingsConfig::findOne(['code' => 'user_wallpaper_id']);
        $usersCount = 0;
        if ($settingConfig) {
            $usersCount = UserSettings::find()
                ->where(['setting_config_id' => $settingConfig->id, 'values' => $id])
                ->count();
        }

        return $this->render('view', [
            'model' => $model,
            'usersCount' => $usersCount
        ]);
    }
}
