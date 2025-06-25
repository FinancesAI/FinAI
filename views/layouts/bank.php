<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\assets\AppAsset;
use app\models\Page;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

AppAsset::register( $this );

$countersMessagesNew = ArrayHelper::getValue($this->params, 'countersMessagesNew');

$this->registerCss('
.bank-loans.in-progress.btn-warning {background-color: ' . Yii::$app->setting->get("bank_loans_inprogress_color") . ' !important;}
.bank-loans.reject.btn-danger {background-color: ' . Yii::$app->setting->get("bank_loans_reject_color") . ' !important;}
.bank-loans.accept.btn-success {background-color: ' . Yii::$app->setting->get("bank_loans_accept_color") . ' !important;}
.bank-loans.btn-default {background-color: ' . Yii::$app->setting->get("bank_loans_default_color") . ' !important;}
');

if (Yii::$app->request->get('mode') == 'test') {
    $this->registerJs("var testMode = true;", View::POS_HEAD);
}

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow"/>
    <meta name="baseUrl" content="<?= \yii\helpers\Url::to(['/'], true) ?>">
	<?= Html::csrfMetaTags() ?>
    <title><?= Html::encode( $this->title ) ?> - <?= Yii::$app->setting->get( "application_name" ) ?></title>
	<?php $this->head() ?>

</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
	<?php
	$nav = NavBar::begin( [
		'brandLabel'            => '<div class="navbar-logo"></div>',
		'innerContainerOptions' => [ 'class' => 'contailer-fluid' ],
		'brandUrl'              => Yii::$app->homeUrl,
		'options'               => [
			'class' => 'navbar-inverse navbar-fixed-top',
		],
	] );

    $items[] = [
            'label' => Yii::t("app/site", "Loans"),
            'url' => Url::toRoute(["/bank-loans"])
        ];

    $items[] = [
        'label' => Yii::t("app/site", "Templates"),
        'url' => Url::toRoute(["/message-template"])
    ];

    $items[] = [
        'label' => Yii::t('app/site', 'Calculators'),
        'url' => Url::toRoute(["/calc"])
    ];

    if (Yii::$app->user->identity->role == 2 && Yii::$app->user->identity->chat_enable == 1) {
        $countersBadge = '&nbsp;' . Html::tag(
                'span',
                $countersMessagesNew,
                [
                    'class' => 'badge messages-new' . ($countersMessagesNew ? '' : ' hidden'),
                    'data-count' => $countersMessagesNew
                ]
            );

        $items[] =[
            'label' => Yii::t( "app/site", "Chat" ) . $countersBadge,
            'url'   => Url::toRoute( [ "/chat/messages" ] ),
            'encode' => false
        ];
    }

	$items[] = [
		'label'  => Yii::t( "app/site", "Language" ),
		'items'  => [
			[
				'label' => Yii::t( "app/site", "lv" ),
				'url'   => [ '/lang/set?lang=lv' ],
				[ "method" => "post" ]
			],
			[
				'label' => Yii::t( "app/site", "ru" ),
				'url'   => [ '/lang/set?lang=ru' ],
				[ "method" => "post" ]
			],
			[
				'label' => Yii::t( "app/site", "en" ),
				'url'   => [ '/lang/set?lang=en' ],
				[ "method" => "post" ]
			],
		],
		'encode' => false
	];

    $items[] =[
        'label' => Yii::$app->user->identity->fullname,
        'options' => [ 'style' => 'text-transform:capitalize' ],
        'items'  => [
            [ 'label' => Yii::t( "app/site", "Logout" ), 'url' => [ '/user/logout' ], [ "method" => "post" ] ],
        ],
        'encode' => false
    ];

	echo Nav::widget( [
		'options' => [ 'class' => 'navbar-nav navbar-right' ],
		'items'   => $items,
	] );
	NavBar::end();
	?>

    <div class="container-fluid">
		<?= $content ?>
		<?php
		if ( Yii::$app->getSession()->getAllFlashes() ) {
			foreach (Yii::$app->getSession()->getAllFlashes() as $alertType => $alertMsq ) {
				?>
                <div class="alert alert-fixed alert-<?= $alertType ?> alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
					<?= $alertMsq ?>
                </div>
				<?php
			}
		}
		?>
    </div>
</div>

<?= $this->render("_footer") ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
