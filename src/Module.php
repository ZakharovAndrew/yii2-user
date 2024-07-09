<?php

/**
 * Yii2 User
 * *************
 * Yii2 user authentication module for management users and their rights.
 *  
 * @link https://github.com/ZakharovAndrew/yii2-user/
 * @copyright Copyright (c) 2023 Zakharov Andrew
 */
 
namespace ZakharovAndrew\user;

use Yii;

/**
 * User module
 */
class Module extends \yii\base\Module
{    
    /**
     * @var string Module version
     */
    protected $version = "0.4.1";

    /**
     * @var string Alias for module
     */
    public $alias = "@user";
    
    /**
     * @var string version Bootstrap
     */
    public $bootstrapVersion = '';
 
    public $useTranslite = false;
    
    /**
     * @var string show H1
     */
    public $showTitle = true;
    
    /**
     *
     * @var string source language for translation 
     */
    public $sourceLanguage = 'en-US';
    
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'ZakharovAndrew\user\controllers';
    
    /**
     * @var array config access
     */
    public $controllersAccessList = [];

    /**
     * {@inheritdoc}
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }
    
    /**
     * Registers the translation files
     */
    protected function registerTranslations()
    {
        Yii::$app->i18n->translations['extension/yii2-user/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => $this->sourceLanguage,
            'basePath' => '@vendor/zakharov-andrew/yii2-user/src/messages',
            'on missingTranslation' => ['app\components\TranslationEventHandler', 'handleMissingTranslation'],
            'fileMap' => [
                'extension/yii2-user/user' => 'user.php',
            ],
        ];
    }

    /**
     * Translates a message. This is just a wrapper of Yii::t
     *
     * @see Yii::t
     *
     * @param $category
     * @param $message
     * @param array $params
     * @param null $language
     * @return string
     */
    public static function t($message, $params = [], $language = null)
    {
        $category = 'user';
        return Yii::t('extension/yii2-user/' . $category, $message, $params, $language);
    }
    
}
