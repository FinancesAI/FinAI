<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\LoginAsset;

LoginAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style type="text/css">
        body, .form-control {
            color: #384d8e;
        }
    	.background-container {
		    position: absolute;
		    width: 100%;
		    height: 100%;
            background: url(/images/background.png) no-repeat center bottom;
            background-size: cover;
    	}
    	.credentials-background {
		    position: absolute;
		    height: 100%;
		    width: 100%;
		    transition: .6s all ease-in;
    	}
    	.container h1 {
    		margin-top: 0px;
    	}
    	.help-block {
    		display: none !important;
    	}
    	.container {
		    width: 300px;
		    height: 280px;
		    position: absolute;
		    z-index: 10000000;
		    background-color: #fff;
		    padding: 20px;
		    left: 0;
		    right: 0;
		    margin: auto;
		    top: 50%;
		    margin-top: -150px;
		    border-radius: 5px;
    	}
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
	<div class="background-container">
    </div>
    <div class="container">
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
