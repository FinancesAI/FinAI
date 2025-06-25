<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;

use app\models\Bill;
use app\models\Loan;
use app\models\Changes;
use app\models\LoanExtra;
use app\controllers\MailController;
use app\models\Person;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class TmpController extends Controller
{
    /**
     * This command updates waiting time
     */
    public function actionReminderTime()
    {
    	//\Yii::$app->db->createCommand("UPDATE `loan` SET `reminder_time` = null WHERE `reminder_time` IS NOT NULL")->execute();
    }
    
    public function actionRejectOld() {
    	
    	$loans = Loan::find()->where(["product" => 2])->andWhere(["<", "create_time", strtotime("-7days")])->andWhere(["status" => Loan::STATUS_IN_PROGRES])->all();
    	var_dump("SKAITS: ".count($loans));
    	foreach ($loans as $loan) {
    		if (!$loan->extra->bank_account_statement) {
    			$rLoan = Loan::find()->where(["id" => $loan->id])->one();
    			
    			$rLoan->status = 4;
    			$rLoan->description = "Manual script reject after request (08.05.2017)";
    			if (MailController::send($rLoan, "25")) {
    				$rLoan->afterFind();
    				if ($rLoan->save()) {
    					var_dump($rLoan->id);
    				}
    			}
    			
    		}
    	}
    }
    
    public function actionMig() {
    	$loans = Loan::find()->joinWith(["changes"])
	->andWhere(["changes.attr" => "status"])
	->andWhere(["changes.attr_from" => 7])
	->andWhere(["changes.attr_to" => 0])
	->groupBy("changes.type_id")->all();

//var_dump(count($loans));
//exit;

	//select * from changes where create_time>1493792418 and attr="status" and attr_from=4 and attr_to=0 limit 1000;    	
    	foreach ($loans as $loan) {
    		if ($loan->status == 0) {
    			var_dump($loan->id);
    			$loan->status = 7;
    			$loan->description = "Fixed bug, so rejected";
    			$loan->save();
    		}
    	}
    }
    
    public function actionFixMis()
    {
    	$loans = Loan::find()->where(["product" => 1])->andWhere([">", "update_time", strtotime("-3 hours")])->all();
    	foreach ($loans as $loan) {
    		$changes = Changes::find()->where(["type" => LoanExtra::TYPE, "type_id" => $loan->id])->all();
    		foreach ($changes as $change) {
    			if (strpos($change->attr, 'car_owner_address') !== false) {
    				if ($change->attr_from == "LV-" || $change->attr_from == "" || $change->attr_from == " LV-") {
    					continue;
    				}
    				var_dump($change->attr_from);
    				$loan->extra->setAttribute($change->attr, $change->attr_from);
    			}
    		}
    		$loan->extra->save();
    	}
    }
    
    /**
     * This command updates waiting time
     */
    public function actionRejectTmp()
    {
    	//$loans = Loan::find()->where(["status" => 1])->all();
    	//foreach ($loans as $loan) {
    	//	$loan->status = 4;
    	//	$loan->description = "Auto rejected by request";
    	//	echo $loan->save();
    	//}
    }
    
    public function actionTime() {
\Yii::$app->getMailer()->setTransport(\Yii::$app->params["mailer"]["onefinance"]);
    	\Yii::$app->mailer->compose()
    	->setFrom('info@onefinance.lv')
    	->setTo('yam.aka.as@gmail.com')
    	->setSubject('test time')
    	->send();
    }
}
