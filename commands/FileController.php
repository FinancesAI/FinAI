<?php

namespace app\commands;

use app\components\services\TFBankService;
use app\models\Loan;

class FileController extends \yii\console\Controller {
	public function actionIndex() {

		$loan = Loan::find()->where( [ 'id' => 2132 ] )->one();

		$tfBankService = new TFBankService( $loan );


		$res = $tfBankService->sendDocument('50456708');

		print_r($res);

		return 'ok';
	}

}
