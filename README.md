# Yii2 user

[![Latest Stable Version](https://poser.pugx.org/zakharov-andrew/yii2-user/v/stable)](https://packagist.org/packages/zakharov-andrew/yii2-user)
[![Total Downloads](https://poser.pugx.org/zakharov-andrew/yii2-user/downloads)](https://packagist.org/packages/zakharov-andrew/yii2-user)
[![License](https://poser.pugx.org/zakharov-andrew/yii2-user/license)](https://packagist.org/packages/zakharov-andrew/yii2-user)
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)

Yii2 user authentication module for management users and their rights.

- Registration, authorization, password recovery and so on
- User administration interface
- Supports role creation
- Multiple user roles are supported
- Supports languages: English, Russian

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ composer require zakharov-andrew/yii2-user
```
or add

```
"zakharov-andrew/yii2-user": "*"
```

to the ```require``` section of your ```composer.json``` file.

Subsequently, run

```
./yii migrate/up --migrationPath=@vendor/zakharov-andrew/yii2-user/migrations
```

in order to create the settings table in your database.

Or add to console config

```php
return [
    // ...
    'controllerMap' => [
        // ...
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => [
                '@console/migrations', // Default migration folder
                '@vendor/zakharov-andrew/yii2-user/src/migrations'
            ]
        ]
        // ...
    ]
    // ...
];
```

## Usage

Add this to your main configuration's modules array

```php
    'modules' => [
        'user' => [
            'class' => 'ZakharovAndrew\user\Module',
            'bootstrapVersion' => 5, // if use bootstrap 5
            'showTitle' => true, // display H1 headings (default - true)
            'telegramToken' => '', // necessary for the bot to work
            'telegramBotLink' => 'https://t.me/YOUR_BOT_NAME_FOR_USER_LINK', //change!
            // use for menu and access
            'controllersAccessList' => [
                1001 => [
                            'Users' => [
                                '/user/user/index' => 'users',
                                '/user/user/create' => 'create user',
                            ],
                        ], 
                1002 => ['/user/roles/index' => 'Roles']
            ]
        ],
        // ...
    ],
```

Add this to your ```config\params.php```

```php
return [
    // ...
    'supportEmail' => 'change-this-email@test.com',
    // lifetime of the password reset token
    'userResetPasswordTokenExpire' => 3600
    // ...
];
```

**If a pretty URL is enabled:**

Add this to your main configuration's urlManager array

```php
'urlManager' => [
    //...
    'rules' => [
        'login' => 'user/user/login',
        'logout' => 'user/user/logout',
        'profile' => 'user/user/profile',
        //...
    ],
    //...
],
```

## Happy birthday widget

You can use the birthday greeting widget by customizing both the header and the message indicating that there are no birthdays today:

```php
<?= \ZakharovAndrew\users\components\BirthdayWidget::widget([
    'headerMessage' => 'Today’s birthdays:',
    'noBirthdaysMessage' => 'Today, no one is celebrating a birthday.' // or empty
]); ?>
```

## License

**yii2-user** it is available under a MIT License. Detailed information can be found in the `LICENSE.md`.
