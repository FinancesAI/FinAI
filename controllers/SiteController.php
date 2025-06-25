<?php

namespace app\controllers;

use app\models\search\StatsSearch;
use app\services\SourceService;
use http\Url;
use Yii;
use app\base\Controller;

class SiteController extends Controller {
	public $layout = 'main';

	public function actions() {
		return [
			'error'   => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
			],
		];
	}

	public function actionIndex() {
		$data = [
			'form_id'      => 1,
			'form_post_id' => 1765,
			'form_value'   => unserialize( 'a:21:{s:12:"cfdb7_status";s:6:"unread";s:5:"phone";s:8:"20263453";s:5:"email";s:16:"sinema1@inbox.lv";s:4:"imia";s:15:"Janis Juhnevics";s:13:"personal-code";s:12:"010696-12414";s:4:"suma";s:4:"1500";s:4:"srok";s:1:"2";s:5:"netto";s:4:"1400";s:6:"platez";s:3:"100";s:9:"izdivency";s:1:"0";s:8:"zarplata";s:4:"1500";s:11:"darbavietas";s:11:"Dackiftarna";s:6:"adrese";s:9:"Zviedrija";s:5:"amats";s:10:"Stradnieks";s:5:"stazs";s:6:"2 gadi";s:12:"checkbox-470";a:1:{i:0;s:3:"Nē";}s:7:"privacy";s:1:"1";s:11:"predlozenie";a:1:{i:0;s:51:"Es piekrītu saņemt piedāvājumus un informāciju";}s:6:"kurjer";a:1:{i:0;s:26:"Saņemt līgumu ar kurjeru";}s:18:"documentcfdb7_file";s:26:"1559238363-statement-3.pdf";s:14:"mc4wp_checkbox";s:2:"No";}' )
		];

		$formId = $data["form_post_id"];

//		dd( [
//			'form_id'      => $data['form_id'],
//			'form_post_id' => $data['form_post_id'],
//			'form_value'   => $data['form_value'],
//			'source' => \Yii::$app->params['wordpress']['forms'][ $formId ]
//		] );

//	    dd(Yii::$app->params);

		if ( ! Yii::$app->getUser()->getIdentity()->isAdmin() ) {
			//return $this->redirect(Url::toRoute(["/loan/index"]).Yii::$app->setting->get("default_loan_url"));
		}

		if ( Yii::$app->getUser()->getIdentity()->isBank() ) {
			return $this->redirect(['/bank-loans/index']);
		}

		$searchModel = new StatsSearch();
		if ( Yii::$app->getRequest()->post( "task" ) == "load-more" ) {
			return $searchModel->compareMore( Yii::$app->getRequest()->post( "offset" ) );
		}

		$data         = $searchModel->statusBarData();
		$clientsCount = $searchModel->clientsCount();
		$loansCount   = $searchModel->loansCount();
		$loansClosed  = $searchModel->loansClosedCount();
		$dealStages   = $searchModel->getDealStages();
        unset($dealStages[0]);
		$sources      = $searchModel->getSources();

        $sources = array_filter($sources, function ($var) {
            return !empty($var) && !is_numeric($var);
        });

//        $params = Yii::$app->params['wordpress']['forms'];
        $params = SourceService::getWordpressForms();
        $formNames = array_column($params, 'order', 'name');

        usort($sources, function ($a, $b) use ($formNames) {

            if (!isset($formNames[$a]) || !isset($formNames[$b])) {
                return 0;
            }

            if ($formNames[$a] == $formNames[$b]) {
                return 0;
            }
            return ($formNames[$a] < $formNames[$b]) ? -1 : 1;
        });

		$products     = $searchModel->getProducts();
        unset($products[0]);
		$compare      = $searchModel->compare();
		$tooltip      = $searchModel->getTooltip();

        $partners = $searchModel->getInProgress();
        unset($partners[0]);

		$total       = $searchModel->getSumClosedLoan( );
		$month       = $searchModel->getSumClosedLoan( "month" );
		$week        = $searchModel->getSumClosedLoan( "week" );
		$searchModel = $searchModel->search( Yii::$app->request->queryParams );

		return $this->render( 'index', [
			"data"        => $data,
			"searchModel" => $searchModel,
			"total"       => $total,
			"month"       => $month,
			"week"        => $week,
			"loans"       => $loansCount,
			"clients"     => $clientsCount,
			"loansClosed" => $loansClosed,
			"products"    => $products,
			"sources"     => $sources,
			"dealStages"  => $dealStages,
			"compare"     => $compare,
			"tooltip"     => $tooltip,
            "partners"    => $partners
		] );
	}
}
