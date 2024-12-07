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
    protected $version = "0.5.8";

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
     *
     * @var string Telegram Token
     */
    public $telegramToken = '';
    
    /**
     *
     * @var string Telegram Bot link
     */
    public $telegramBotLink = '';
    
    /**
     *
     * @var string Path where avatars are stored
     */
    public $avatarPath = '/avatars/';

    /**
     * {@inheritdoc}
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        
        self::registerTranslations();
        
        // Registering an alias
        Yii::setAlias($this->alias, __DIR__);
    }
    
    /**
     * Registers the translation files
     */
    protected static function registerTranslations()
    {
        if (isset(Yii::$app->i18n->translations['extension/yii2-user/*'])) {
            return;
        }
        
        Yii::$app->i18n->translations['extension/yii2-user/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
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
        static::registerTranslations();
        
        $category = 'user';
        return Yii::t('extension/yii2-user/' . $category, $message, $params, $language);
    }
    
}
