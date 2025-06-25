<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;

use app\models\Bill;
use app\controllers\SmsController;
use app\models\Loan;
use app\controllers\MailController;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BillController extends Controller
{
    /**
     * This command updates waiting time
     */
    public function actionSend()
    {
		$bills = Bill::find()->where(["<", "due_time",  strtotime("-2 days")])->andFilterWhere(["status" => 0])->all();

		$text = "";
		
		foreach ($bills as $bill) {
			if ($bill->loan->status == Loan::STATUS_REJECTED || $bill->loan->status == Loan::STATUS_REFUSES) {
				$text .= "Skip jo status rejected: ".$bill->loan_id." (".$bill->id.")<br />";
				continue;
			}
			if ($bill->due_time < strtotime("-30 days")) {
				
				try {
					$this->_sendAlert($bill);
					$text .= "Sanema alert emailu: ".$bill->loan_id." (".$bill->id.")<br />";
				} catch(\Exception $e) {
					$text .= "Nesanema alert emailu, errors (".$e->getMessage()."): ".$bill->loan_id." (".$bill->id.")<br />";
				}
			} else {
				try {
					$text .= "Sanema info emailu: ".$bill->loan_id." (".$bill->id.")<br />";
					$this->_sendInfo($bill);
				} catch(\Exception $e) {
					$text .= "Nesanema info emailu, errors (".$e->getMessage()."): ".$bill->loan_id." (".$bill->id.")<br />";
				}

			}
			//echo $bill->loan_id."\r\n";
		}
		
		\Yii::$app->getMailer()->setTransport(\Yii::$app->params["mailer"]["system"]);
		$mailer = \Yii::$app->mailer->compose()
		                            ->setFrom('info@brokerlatfinance.lv')
		                            ->setTo('marcis.petters@gmail.com')
		                            ->setSubject("Cronjob bill email")
		                            ->setHtmlBody($text)->send();
    }
    
    public function actionSendTest()
    {
    	$bills = Bill::find()->where(["<", "due_time",  strtotime("-2 days")])->andFilterWhere(["status" => 0])->all();
    
    	$text = "";
    
    	foreach ($bills as $bill) {
    		if ($bill->loan->status == Loan::STATUS_REJECTED) {
    			$text .= "Skip jo status rejected: ".$bill->loan_id." (".$bill->id.")<br />";
    			continue;
    		}
    		if ($bill->due_time < strtotime("-30 days")) {
    			$text .= "Sanema alert emailu: ".$bill->loan_id." (".$bill->id.")<br />";
    		} else {
    			$text .= "Sanema info emailu: ".$bill->loan_id." (".$bill->id.")<br />";
    		}
    	}
    
    	\Yii::$app->getMailer()->setTransport(\Yii::$app->params["mailer"]["system"]);
    	$mailer = \Yii::$app->mailer->compose()
	                                ->setFrom('info@latfinance.lv')
	                                ->setTo('marcis.petters@gmail.com')
	                                ->setSubject("Cronjob bill email")
	                                ->setHtmlBody($text)->send();
    }
    
    private function _sendAlert($bill) {
    	MailController::send($bill->getLoan()->one(), "13");
   		SmsController::send($bill->getLoan()->one(), "6");
    }
    
    private function _sendInfo($bill) {
    	MailController::send($bill->getLoan()->one(), "12");
    	SmsController::send($bill->getLoan()->one(), "5");
    }
    
    public function actionTestTime() {
    	echo "test";
    }
}
