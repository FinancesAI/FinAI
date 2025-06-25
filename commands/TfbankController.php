<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;

use app\models\Loan;
use app\models\Person;
use app\models\LoanExtra;
use app\components\XLSXWriter;
use yii\helpers\Json;
use app\models\User;
use app\components\api\Ntlmservice;
use app\components\api\AizdevumsApi;
use app\components\SolrDataProvider;
use app\models\LoanProgerss;
use app\controllers\MailController;
use app\components\LoanTFBankBehavior;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class TfbankController extends Controller
{
	public function actionSend($loanId) {
		$tfbank = new LoanTFBankBehavior();
		
		$model = Loan::find()->where(["id" => $loanId])->one();
		$tfbank->log("TFBANK AUTO POST START TO SEND LOAN ID - ".$loanId);
		
		try {
			$tfbank->send($model);
		} catch (\Exception $e) {
			$tfbank->log("TFBANK AUTO POST API CONTROLLER ERROR - ".$e->getMessage());
		};
	}
}
