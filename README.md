# Yii2 user

[![Latest Stable Version](https://poser.pugx.org/zakharov-andrew/yii2-user/v/stable)](https://packagist.org/packages/zakharov-andrew/yii2-category)
[![License](https://poser.pugx.org/zakharov-andrew/yii2-user/license)](https://packagist.org/packages/zakharov-andrew/yii2-category)
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)

Yii2 user authentication module for management users and their rights.

- Registration, authorization, password recovery and so on
- User administration interface

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

```
    'modules' => [
        'settings' => [
            'class' => 'ZakharovAndrew\user\Module',
            'bootstrapVersion' => 5, // if use bootstrap 5
        ],
        // ...
    ],
```

## License

**yii2-user** it is available under a MIT License. Detailed information can be found in the `LICENSE.md`.
