<?php

namespace app\models;

use Yii;

require_once __DIR__ . '/../vendor/autoload.php';


define('APPLICATION_NAME', 'Gmail API PHP Quickstart');
define('CREDENTIALS_PATH',   __DIR__ . '/../credentials.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/../client_secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/gmail-php-quickstart.json
define('SCOPES', implode(' ', array(
		\Google_Service_Gmail::GMAIL_READONLY)
));

// if (php_sapi_name() != 'cli') {
// 	throw new Exception('This application must be run on the command line.');
// }

class Gmail {
	
	/**
	 * Loan model
	 * @var Loan
	 */
	protected $_model;
	
	public function setModel($model) {
		$this->_model = $model;
	}
	
	public function getMessages() {
		// Get the API client and construct the service object.
		$client = $this->getClient();
		$gmail = new \Google_Service_Gmail($client);

		$list = $gmail->users_messages->listUsersMessages('me', ['q' => "from:".$this->_model->person->email]);
		
		try {
			while ($list->getMessages() != null) {
		
				foreach ($list->getMessages() as $mlist) {
		
					$message_id = $mlist->id;
					$optParamsGet2['format'] = 'full';
					$single_message = $gmail->users_messages->get('me', $message_id, $optParamsGet2);
					$payload = $single_message->getPayload();
					$parts = $payload->getParts();
					// With no attachment, the payload might be directly in the body, encoded.
					$body = $payload->getBody();
					$FOUND_BODY = FALSE;
					// If we didn't find a body, let's look for the parts
					if(!$FOUND_BODY) {
						foreach ($parts  as $part) {
							if($part['parts'] && !$FOUND_BODY) {
								foreach ($part['parts'] as $p) {
									if($p['parts'] && count($p['parts']) > 0){
										foreach ($p['parts'] as $y) {
											if(($y['mimeType'] === 'text/html') && $y['body']) {
												$FOUND_BODY = $this->decodeBody($y['body']->data);
												break;
											}
										}
									} else if(($p['mimeType'] === 'text/html') && $p['body']) {
										$FOUND_BODY = $this->decodeBody($p['body']->data);
										break;
									}
								}
							}
							if($FOUND_BODY) {
								break;
							}
						}
					}
					// let's save all the images linked to the mail's body:
					if($FOUND_BODY && count($parts) > 1){
						$images_linked = array();
						foreach ($parts  as $part) {
							if($part['filename']){
								array_push($images_linked, $part);
							} else{
								if($part['parts']) {
									foreach ($part['parts'] as $p) {
										if($p['parts'] && count($p['parts']) > 0){
											foreach ($p['parts'] as $y) {
												if(($y['mimeType'] === 'text/html') && $y['body']) {
													array_push($images_linked, $y);
												}
											}
										} else if(($p['mimeType'] !== 'text/html') && $p['body']) {
											array_push($images_linked, $p);
										}
									}
								}
							}
						}
						// special case for the wdcid...
						preg_match_all('/wdcid(.*)"/Uims', $FOUND_BODY, $wdmatches);
						if(count($wdmatches)) {
							$z = 0;
							foreach($wdmatches[0] as $match) {
								$z++;
								if($z > 9){
									$FOUND_BODY = str_replace($match, 'image0' . $z . '@', $FOUND_BODY);
								} else {
									$FOUND_BODY = str_replace($match, 'image00' . $z . '@', $FOUND_BODY);
								}
							}
						}
						preg_match_all('/src="cid:(.*)"/Uims', $FOUND_BODY, $matches);
						if(count($matches)) {
							$search = array();
							$replace = array();
							// let's trasnform the CIDs as base64 attachements
							foreach($matches[1] as $match) {
								foreach($images_linked as $img_linked) {
									foreach($img_linked['headers'] as $img_lnk) {
										if( $img_lnk['name'] === 'Content-ID' || $img_lnk['name'] === 'Content-Id' || $img_lnk['name'] === 'X-Attachment-Id'){
											if ($match === str_replace('>', '', str_replace('<', '', $img_lnk->value))
													|| explode("@", $match)[0] === explode(".", $img_linked->filename)[0]
													|| explode("@", $match)[0] === $img_linked->filename){
												$search = "src=\"cid:$match\"";
												$mimetype = $img_linked->mimeType;
												$attachment = $gmail->users_messages_attachments->get('me', $mlist->id, $img_linked['body']->attachmentId);
												$data64 = strtr($attachment->getData(), array('-' => '+', '_' => '/'));
												$replace = "src=\"data:" . $mimetype . ";base64," . $data64 . "\"";
												$FOUND_BODY = str_replace($search, $replace, $FOUND_BODY);
											}
										}
									}
								}
							}
						}
					}
					// If we didn't find the body in the last parts,
					// let's loop for the first parts (text-html only)
					if(!$FOUND_BODY) {
						foreach ($parts  as $part) {
							if($part['body'] && $part['mimeType'] === 'text/html') {
								$FOUND_BODY = $this->decodeBody($part['body']->data);
								break;
							}
						}
					}
					// With no attachment, the payload might be directly in the body, encoded.
					if(!$FOUND_BODY) {
						$FOUND_BODY = $this->decodeBody($body['data']);
					}
					// Last try: if we didn't find the body in the last parts,
					// let's loop for the first parts (text-plain only)
					if(!$FOUND_BODY) {
						foreach ($parts  as $part) {
							if($part['body']) {
								$FOUND_BODY = $this->decodeBody($part['body']->data);
								break;
							}
						}
					}
					if(!$FOUND_BODY) {
						$FOUND_BODY = '(No message)';
					}
					
					// Finally, print the message ID and the body
					echo "<h1>Message with ID: <a href='https://mail.google.com/mail/u/0/#inbox/".$message_id."' target='_blank'>".$message_id."</a></h1>";
					print_r($FOUND_BODY);
					echo "<hr />";
				}
		
				if ($list->getNextPageToken() != null) {
					$pageToken = $list->getNextPageToken();
					$list = $gmail->users_messages->listUsersMessages('me', ['pageToken' => $pageToken, 'maxResults' => 1000]);
				} else {
					break;
				}
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	/**
	 * Returns an authorized API client.
	 * @return Google_Client the authorized client object
	 */
	private function getClient() {
		$client = new \Google_Client();
		$client->setApplicationName(APPLICATION_NAME);
		$client->setScopes(SCOPES);
		$client->setAuthConfigFile(CLIENT_SECRET_PATH);
		$client->setAccessType('offline');
	
		// Load previously authorized credentials from a file.
		$credentialsPath = $this->expandHomeDirectory(CREDENTIALS_PATH);
		if (file_exists($credentialsPath)) {
			$accessToken = file_get_contents($credentialsPath);
		} else {
			// Request authorization from the user.
			$authUrl = $client->createAuthUrl();
			printf("Open the following link in your browser:\n%s\n", $authUrl);
			print 'Enter verification code: ';
			$authCode = trim(fgets(STDIN));
	
			// Exchange authorization code for an access token.
			$accessToken = $client->authenticate($authCode);
	
			// Store the credentials to disk.
			if(!file_exists(dirname($credentialsPath))) {
				mkdir(dirname($credentialsPath), 0700, true);
			}
			file_put_contents($credentialsPath, $accessToken);
			printf("Credentials saved to %s\n", $credentialsPath);
		}
		
		$client->setAccessToken($accessToken);
	
		// Refresh the token if it's expired.
		if ($client->isAccessTokenExpired()) {
			$client->refreshToken($client->getRefreshToken());
			file_put_contents($credentialsPath, $client->getAccessToken());
		}
		return $client;
	}
	
	/**
	 * Expands the home directory alias '~' to the full path.
	 * @param string $path the path to expand.
	 * @return string the expanded path.
	 */
	private function expandHomeDirectory($path) {
		$homeDirectory = getenv('HOME');
		if (empty($homeDirectory)) {
			$homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
		}
		return str_replace('~', realpath($homeDirectory), $path);
	}
	
	/**
	 * Decode the body.
	 * @param : encoded body  - or null
	 * @return : the body if found, else FALSE;
	 */
	private function decodeBody($body) {
		$rawData = $body;
		$sanitizedData = strtr($rawData,'-_', '+/');
		$decodedMessage = base64_decode($sanitizedData);
		if(!$decodedMessage){
			$decodedMessage = FALSE;
		}
		return $decodedMessage;
	}
}