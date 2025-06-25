<?php 
namespace app\components\modules;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use app\models\Loan;
use app\controllers\SmsController;

class LoanWelcomeSmsBehavior extends Behavior
{
	public function events()
	{
		return [
				ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
		];
	}
	
	public function afterInsert($event)
	{
		if ($event->sender->source == "onefinance" || $event->sender->source == "1aizdevums") {
			if ((int)$event->sender->person->credit_history == 3 || (int)$event->sender->person->credit_history == 1) {
				$this->_log("START - ".$event->sender->source." - ".$event->sender->id);
				//SmsController::send(Loan::find()->where(["id" => $event->sender->id])->one(), "9", false, false);
				$this->_log("END");
			}
		}
	}
	
    private function _log($msg) {
		$fd = fopen(\Yii::$app->params["welcomesms_log_path"], "a+");
    	$str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
    	fwrite($fd, $str . "\n");
    	fclose($fd);
    }
}
