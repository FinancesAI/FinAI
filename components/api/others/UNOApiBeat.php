<?php 
class UNOApiBeat {
	
	public static function beat() {
		self::log("BEAT START");
		
		$fullUrl = self::params("unoapi_beat");
		
		$result = @file_get_contents($fullUrl);
		if ($result) {
			self::log("BEAT OK - ".$fullUrl." - ANSWER - ".$result);
		} else {
			self::log("BEAT ERROR - ".$fullUrl);
		}
		
		self::log("BEAT END");
	}
	
	public static function log($msg) {
		$fd = fopen(self::params("unoapi_log_path"), "a+");
		$str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
		fwrite($fd, $str . "\n");
		fclose($fd);
	}
	
	public static function params($param) {
		$config = require(__DIR__ . '/../../../config/params.php');
		return $config[$param];
	}
}

UNOApiBeat::beat();