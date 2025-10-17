# Yii2 user

![Yii2 user module by Zakharov Andrey](docs/img/yii-2-user-module-Zakharov-Andrey.png)

[![Latest Stable Version](https://poser.pugx.org/zakharov-andrew/yii2-user/v/stable)](https://packagist.org/packages/zakharov-andrew/yii2-user)
[![Total Downloads](https://poser.pugx.org/zakharov-andrew/yii2-user/downloads)](https://packagist.org/packages/zakharov-andrew/yii2-user)
[![License](https://poser.pugx.org/zakharov-andrew/yii2-user/license)](https://packagist.org/packages/zakharov-andrew/yii2-user)
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)

Yii2 user authentication module for management users and their rights.

- Registration, authorization, password recovery, change email and so on
- User administration interface
- Supports role creation
- Multiple user roles are supported
- Happy Birthday widgets
- Birthday Calendar widget
- User Deputies Management
- logging of failed authorization attempts and blocking access via IP
- Supports languages: English, Russian

## 🚀 Installation

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

## 🛠 Usage

Add this to your main configuration's modules array

```php
    'modules' => [
        'user' => [
            'class' => 'ZakharovAndrew\user\Module',
            'bootstrapVersion' => 5, // if use bootstrap 5
            'showTitle' => true, // display H1 headings (default - true)
            'enableUserSignup' => false, //Toggles user registration functionality (default - false)
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
            ],
            'wallpapers' => [
                [
                    'url' => 'path/to/wallpaper1.jpg',
                    'roles' => ['user', 'admin'], // available to which roles
                ],
                [
                    'url' => 'path/to/wallpaper2.jpg',
                    'roles' => ['admin'], // only for admin
                ],
            // ...
            ],
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
        'signup' => 'user/user/signup',
        'wallpapers' => 'user/wallpaper/index',
        //...
    ],
    //...
],
```

## 🎉 Happy Birthday widget

You can use the birthday greeting widget by customizing both the header and the message indicating that there are no birthdays today:

```php
<?= \ZakharovAndrew\user\components\BirthdayWidget::widget([
    'headerMessage' => 'Today’s birthdays:',
    'noBirthdaysMessage' => 'Today, no one is celebrating a birthday.', // or empty
    'useAvatars' => true
]); ?>
```

Widget for congratulating the user on his birthday:

```php
<?= \ZakharovAndrew\user\components\BirthdayGreetingWidget::widget([
    'message' => '<h1>Happy Birthday, {username}!</h1>'
]) ?>
```

### 📅 Birthday Calendar Widget

Display a calendar of birthdays for the current week and next month:

```php
<?= \ZakharovAndrew\user\widgets\BirthdayCalendarWidget::widget([
    'title' => 'Upcoming Birthdays',
    'showAge' => true,
    'maxUsersPerDay' => 3
]); ?>
```

**Options:**
- title (string) - Calendar title
- showAge (bool) - Show user age (default: true)
- maxUsersPerDay (int) - Maximum number of users to show per day (default: 5)

**Features:**

- Shows birthdays from current week to next month
- 📆 Weekly grouping with week numbers
- 🎯 Current day highlighting
- 👥 Clickable user names linking to profiles
- 🔢 Age display with proper pluralization
- 📱 Responsive design for mobile devices
- 🌍 Multiple language support (English/Russian)

Advanced usage with custom parameters:

```php
<?= \ZakharovAndrew\user\widgets\BirthdayCalendarWidget::widget([
    'title' => \ZakharovAndrew\user\Module::t('Upcoming Birthdays'),
    'showAge' => true,
    'maxUsersPerDay' => 5,
    'view' => 'custom-calendar-view' // custom view file
]); ?>
```

## 👤 User Menu Widget

Add a user menu to your navigation bar with avatar, name and dropdown options:

### Basic Usage

```php
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Your App</a>
        
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <!-- Your menu items -->
            </ul>
            
            <ul class="navbar-nav">
                <?= \ZakharovAndrew\user\widgets\UserMenuWidget::widget() ?>
            </ul>
        </div>
    </div>
</nav>
```

### Custom Guest Content

Customize the display for non-authenticated users:

```php
<?= \ZakharovAndrew\user\widgets\UserMenuWidget::widget([
    'guestContent' => '
        <div class="d-flex gap-2">
            <a href="' . \yii\helpers\Url::to(['/user/auth/login']) . '" class="btn btn-outline-light btn-sm">
                ' . \ZakharovAndrew\user\Module::t('Login') . '
            </a>
            <a href="' . \yii\helpers\Url::to(['/user/auth/signup']) . '" class="btn btn-primary btn-sm">
                ' . \ZakharovAndrew\user\Module::t('Signup') . '
            </a>
        </div>
    ',
]) ?>
```

### Advanced Configuration

```php
<?= \ZakharovAndrew\user\widgets\UserMenuWidget::widget([
    'guestView' => 'custom-guest-view', // custom view file
    'guestContent' => [
        'loginUrl' => ['/user/auth/login'],
        'signupUrl' => ['/user/auth/signup'],
    ],
]) ?>
```

## 👥 User Deputies Management
The module now supports user deputies functionality, allowing users to have multiple deputies with date tracking and assignment history.

### Basic Usage

```php
// Get current user
$user = User::findOne(1);

// Add a deputy with validity period
$user->addDeputy(2, '2024-01-01', '2024-12-31');

// Get all active deputies for a user
$deputies = $user->getCurrentDeputies();

// Check if user has specific deputy
$hasDeputy = $user->hasDeputy(2);

// Check if user is deputy for another user
$isDeputy = $user->isDeputyFor(1);

// Remove deputy
$user->removeDeputy(2);

// Get users for whom current user is a deputy
$deputyForUsers = $user->getCurrentDeputyForUsers();
```

### Advanced Usage

```php
// Add deputy with custom created by user
$user->addDeputy(2, '2024-01-01', null, Yii::$app->user->id);

// Get deputies list for dropdown
$deputiesList = $user->getDeputiesList();

// Get available users for deputy assignment
$availableUsers = User::getAvailableUsersForDeputy($currentUserId);

// Check if deputy relation is currently active
$deputy = UserDeputy::findOne(1);
$isActive = $deputy->isCurrentlyActive();
```

### Features
- ✅ Multiple deputies - Users can have multiple deputies
- ✅ Date tracking - Track assignment dates and validity periods
- ✅ Assignment history - Record who assigned each deputy
- ✅ Active/inactive status - Manage deputy status without deletion
- ✅ Date validation - Automatic validation of validity periods
- ✅ Relationship management - Easy methods for managing deputies

## 👥 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

**yii2-user** it is available under a MIT License. Detailed information can be found in the `LICENSE.md`.
