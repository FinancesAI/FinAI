<?php 
namespace app\components\modules;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use app\models\Loan;

class LoanPropelleradsBehavior extends Behavior
{
	public function events()
	{
		return [
				ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
		];
	}
	
	public function afterInsert($event)
	{
		if ($event->sender->query_string) {
			parse_str($event->sender->query_string, $array);
			if (isset($array["aff_sub"]) && $event->sender->source) {
				if ($array["aff_sub"] !== "") {
				    $this->_postBack($array["aff_sub"]);
				}
			}
		}
	}
	
	private function _postBack($visitor) {
		$result = @file_get_contents("http://ad.propellerads.com/conversion.php?aid=122619&pid=&tid=29705&visitor_id=".$visitor);
		if ($result) {
			$this->_log("OK PROPERLLERADS -  - ANSWER - ".json_encode($result));
		} else {
			$this->_log("ERROR PROPERLLERADS - ");
		}
	}
	
    private function _log($msg) {
		$fd = fopen(\Yii::$app->params["postback_log_path"], "a+");
    	$str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
    	fwrite($fd, $str . "\n");
    	fclose($fd);
    }
}