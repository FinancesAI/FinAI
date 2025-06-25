<?php 

namespace cronpost\CronPost;
/**
 * This class should be extaned
 * Extand save and/or error functions
 * Post array should be in this format and thease are requared fields
 * "Loan" => [
 * 		"amount"  => "1000",
 * 		"term" => "12",
 * ],
 * "Person" => [
 * 		"name" => "TestName",
 * 		"surname" => "TestSurname",
 * 		"personal_code" => "123456-12345612",
 * 		"phone" => "1234567890",
 * 		"email" => "TestName@example.com",
 * 		"income" => "1000",
 * 		"outcome" => "1000",
 * 		"dependants" => "1",
 * ]
 */
class CronPost {

	/**
	 * Url where to post
	 * @var string
	 */
	protected $url;
	
	/**
	 * Setup global varibles 
	 * @param string $url
	 * @return void
	 */
	final function __construct($url) {
		$this->url = $url;
	}
	
	/**
	 * Post data
	 * @param array $data
	 */
	final function post($data) {
		$handle = curl_init($this->url);
		
		curl_setopt_array($handle, [				
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER         => false,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_MAXREDIRS      => 10, 
				CURLOPT_ENCODING       => "",
				CURLOPT_USERAGENT      => "test",
				CURLOPT_AUTOREFERER    => true,
				CURLOPT_CONNECTTIMEOUT => 120,
				CURLOPT_TIMEOUT        => 120,
		]);
		
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($data));
		
		$answer = json_decode(curl_exec($handle));
		
		if ($answer && $answer->save) {
			return $this->save($answer, $data);
		} else {
			return $this->error($answer, $data);
		}
	}
	
	/**
	 * Trigger answer saved
	 * @param Object $answer
	 * @return true
	 */
	public function save($answer, $data) {
		return true;
	}
	
	/**
	 * Trigger answer error
	 * @param Object $answer
	 * @return false
	 */
	public function error($answer, $data) {
		return false;
	}
}