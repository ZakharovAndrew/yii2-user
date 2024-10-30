<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\base\Model;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\Module;
use yii\web\UploadedFile;

class ImportForm extends Model
{
    public $csvFile;
    public $status;
    public $separator;
    
    public function rules()
    {
        return [
            [
                'csvFile',
                'file',
                'extensions' => 'csv',
                'maxSize' => 1024 * 1024, // file size should not exceed 1 MB
            ],
            [['separator'], 'string'],
            [['status'], 'in', 'range' => array_keys(User::getStatusList())],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => Module::t('Status'),
            'separator' => Module::t('Separator'). ' ('.Module::t('default').' ;)',
            'csvFile' => 'CSV File',
        ];
    }
    
    public function import()
    {
        if (!$this->validate()) {
            return false;
        }

        $uploadedFile = UploadedFile::getInstance($this, 'csvFile');
        $separator = empty($this->separator) ? ';' : $this->separator;
        $rows = array_map(function($v) use($separator) {return str_getcsv($v, $separator);}, file($uploadedFile->tempName));
        $headers = array_shift($rows);
        
        $result = [];

        foreach ($rows as $row) {
            $data = array_combine($headers, $row);
            $password = User::genPassword();
            
            $user = new User();
            $user->username = $data['username'];
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->setPassword(User::genPassword(8));
            $user->generateTelegramCode(); // set telegram code
            $user->created_by = Yii::$app->user->id;
            $user->status = $this->status;
            
            // Trying to send the password to the email and save the password
            if (!$user->setPassword($password) || !$user->save() || !$user->sendPasswordEmail($password)) {
                // handle validation errors
                $errors = [];
                foreach ($user->getErrors() as $item) {
                    $errors = array_merge($errors, $item);
                }
                $result[] = "<div class=\"alert-danger alert\">{$data['username']} ({$data['email']})".  ' <b>error:</b> ' . implode(' | ', $errors).'</div>';
            } else {
                // add role
                if (isset($data['role_id']) && isset($data['subject_id'])) {
                    
                    $subjects = explode(",", $data['subject_id']);
                    foreach ($subjects as $subject_id) {
                        $role = new UserRoles([
                            'user_id' => $user->id,
                            'role_id' => $data['role_id'],
                            'subject_id' => $subject_id
                        ]);

                        $role->save();
                    }
                }
                
                $result[] = "<div class=\"alert-success alert\">{$data['username']} ({$data['email']})" . ' ' . Module::t('is created').'</div>';;
            }
        }

        return implode('',$result);
    }
}