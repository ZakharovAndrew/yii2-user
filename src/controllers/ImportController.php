<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use ZakharovAndrew\user\controllers\ParentController;
use ZakharovAndrew\user\models\ImportForm;

/**
 * Controller for importing users from a csv file.
 * @author Andrew Zakharov https://github.com/ZakharovAndrew
 */
class ImportController extends ParentController
{
    public $controller_id = 1005;
    
       
    /**
     * Importing users from a csv file
     *
     * @return string
     */
    public function actionImportCsv()
    {
        $model = new ImportForm();
        
        if ($model->load(Yii::$app->request->post())) {
            
            $result = $model->import();
                    
            if (!$model->import()) {
                Yii::$app->getSession()->setFlash('error', 'Ошибка при импортировании пользователей' . var_export($model->getErrors(), true));   
                unset($result);
            }
        }

        return $this->render('import', [
            'model' => $model,
            'result' => $result ?? ''
        ]);
    }

}
