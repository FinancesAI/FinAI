<?php 
class EFinanceApiBeat {
	
	public static function beat() {
		//self::log("BEAT START");
		
		$fullUrl = self::params("efinanceapi_beat");
		
		$postdata = http_build_query(
				array(
						'login' => self::params("efinanceapi_login"),
						'pass' => self::params("efinanceapi_pass"),
						'data' => json_encode([
							"customer_id" => "58057",
						])
				)
		);
		
		$opts = array('http' =>
				array(
						'method'  => 'POST',
						'header'  => 'Content-type: application/x-www-form-urlencoded',
						'content' => $postdata
				)
		);
		
		$context  = stream_context_create($opts);
		
		$result = @file_get_contents($fullUrl, false, $context);

var_dump($result);

		if ($result) {
			self::log($result);
		} else {
			self::log("BEAT ERROR - ".$fullUrl);
		}
		
		//self::log("BEAT END");
	}
	
	public static function log($msg) {
		$fd = fopen(self::params("efinanceapi_log_path"), "a+");
		$str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
		fwrite($fd, $str . "\n");
		fclose($fd);
	}
	
	public static function params($param) {
		$config = require(__DIR__ . '/../../../config/params.php');
		return $config[$param];
	}
}

EFinanceApiBeat::beat();
