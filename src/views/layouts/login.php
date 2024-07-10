<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;
use app\widgets\Alert;

AppAsset::register($this);

$bootstrapVersion = Yii::$app->getModule('user')->bootstrapVersion;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="container">
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php if ($bootstrapVersion==3) { ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php } else { ?>
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
            <?php } ?>
            <?php echo Yii::$app->session->getFlash('success'); ?>
        </div>
    </div>
    <style>
        .container {margin-top:15px}
        .content {padding: 10% !important}
        @media (max-width: 768px) {
            .content {
                padding-left: 20px;
                padding-right: 20px;
                height: 80vh;
            }
        }
    </style>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="container">
        <div class="alert alert-danger alert-dismissible" role="alert">
            <?php if ($bootstrapVersion==3) { ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php } else { ?>
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
            <?php } ?>
            <?php echo Yii::$app->session->getFlash('error'); ?>
        </div>
    </div>
    <style>
        .container {margin-top:15px}
        .content {padding: 10% !important}
        @media (max-width: 768px) {
            .content {
                padding-left: 20px;
                padding-right: 20px;
                height: 80vh;
            }
        }
    </style>
    <?php endif; ?>

    <?= $content ?>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
