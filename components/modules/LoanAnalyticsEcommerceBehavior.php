<?php 
namespace app\components\modules;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use app\models\Loan;

class LoanAnalyticsEcommerceBehavior extends Behavior
{
	public function events()
	{
		return [
				ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
				ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
		];
	}
	
	public function afterInsert($event)
	{
		$url = 'https://www.google-analytics.com/collect';
		$data = [
				'v' => '1',
				'tid' => $this->_tidBySource($event->sender->source),
				'cid' => $event->sender->cid,
				't' => 'item',
				'ti' => $event->sender->id,
				'in' => (isset($event->sender->getProducts()[(int)$event->sender->product]) ? $event->sender->getProducts()[(int)$event->sender->product] : ""),
				'ip' => $event->sender->amount,
				'iq' => 1,
		];
		$options = [
				'http' => [
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($data)
				]
		];
		$context  = stream_context_create($options);
		$result = @file_get_contents($url, false, $context);
			
		$fullUrl = $url."?".http_build_query($data);
		if ($result) {
			$this->_log("SEND ITEM OK - ".$fullUrl." - RESPONSE - ".$result);
		} else {
			$this->_log("SEND ITEM ERROR - ".$fullUrl);
		}
	}
	
	public function afterUpdate($event)
	{
		if ($event->sender->status == Loan::STATUS_CLOSED && $event->sender->update_time == $event->sender->close_time) {
			$url = 'https://www.google-analytics.com/collect';
			$data = [
					'v' => '1',
					'tid' => $this->_tidBySource($event->sender->source),
					'cid' => $event->sender->cid,
					't' => 'transaction',
					'ti' => $event->sender->id,
					'ta' => $event->sender->referral,
					'tr' => $event->sender->amount,
					'ts' => '0', //ship
					'tt' => '0', //tax
					'cu' => 'EUR', // currecy
			];
			$options = [
					'http' => [
							'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
							'method'  => 'POST',
							'content' => http_build_query($data)
					]
			];
			$context  = stream_context_create($options);
			$result = @file_get_contents($url, false, $context);
			
			$fullUrl = $url."?".http_build_query($data);
			if ($result) {
				$this->_log("SEND TRANSACTION OK - ".$fullUrl." - RESPONSE - ".$result);
			} else {
				$this->_log("SEND TRANSACTION ERROR - ".$fullUrl);
			}
		}
	}
	
	private function _tidBySource($title) {
		$campaign = "";
		if ($title == "1lizings") {
			$campaign = "UA-97694515-1";
		}
		if ($title == "1aizdevums") {
			$campaign = "UA-72773945-1";
		}
		if ($title == "onefinance") {
			$campaign = "UA-76947158-1";
		}
		if ($title == "ernio") {
			$campaign = "UA-84650520-1";
		}
		
		return $campaign;
	}
	
    private function _log($msg) {
		$fd = fopen(\Yii::$app->params["analytics_log_path"], "a+");
    	$str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
    	fwrite($fd, $str . "\n");
    	fclose($fd);
    }
}