<?php

namespace app\controllers;

use app\models\Changes;
use app\models\Loan;
use Yii;
use app\base\Controller;
use yii\web\NotFoundHttpException;

/**
 * MailController implements the CRUD actions for Mail model.
 */
class SmsController extends Controller {
	/**
	 * Send mail to loan person.
	 *
	 * @param string $id
	 * @param string $type
	 *
	 * @return mixed
	 */

	public function beforeAction( $action ) {
		if ( $action->id == 'sendextra' || $action->id == 'sendbs' || $action->id == 'sendtest' ) {
			$this->enableCsrfValidation = false;
		}

		return parent::beforeAction( $action );
	}

	public function actionSend() {
		$id = 0;
		if ( Yii::$app->getRequest()->isPost ) {
			$id    = \Yii::$app->getRequest()->post( "id" );
			$type  = \Yii::$app->getRequest()->post( "type" );
			$model = $this->findModel( $id );

			if ( ! $model->source ) {
				\Yii::$app->getSession()->setFlash( "danger", \Yii::t( "app/mail", "SMS not sent: No loan source detected." ) );

				return $this->redirect( [ "loan/view", "id" => $id ] );
			}

			if ( $this->_send( $model, $type ) ) {
				if ( \Yii::$app->getRequest()->isAjax ) {
					return 1;
				}
			} else {
				if ( \Yii::$app->getRequest()->isAjax ) {
					return 0;
				}
			}

			return $this->redirect( [ "loan/view", "id" => $id ] );
		}

		return $this->redirect( [ "loan/view", "id" => $id ] );
	}

	public function actionSend2() {
		ini_set( 'max_execution_time', 0 );
		if ( Yii::$app->getRequest()->isPost ) {
			$type     = \Yii::$app->getRequest()->post( "type" );
			$statuses = \Yii::$app->getRequest()->post( "statuses" );

			$loans = Loan::findAll( [ 'status' => array_values( $statuses ) ] );

			foreach ( $loans as $loan ) {
				if ( ! isset( $loan->source ) && ( $loan->source != '' ) ) {
					\Yii::error( "SMS not sent: No loan source detected. Person_id: " . $loan->person_id );
					continue;
				}

				if ( $this->send( $loan, $type ) ) {
					if ( \Yii::$app->getRequest()->isAjax ) {
						continue;
					}
				} else {
					if ( \Yii::$app->getRequest()->isAjax ) {
						continue;
					}
				}

			}

		}

		return $this->redirect( [ "/loan" ] );

	}

	public function actionSendextra() {
		ini_set( 'max_execution_time', 0 );

		$loans = Loan::findAll( [ 'status' => array_values( [ 0 => '2' ] ) ] );

		foreach ( $loans as $loan ) {
			if ( ! isset( $loan->source ) && ( $loan->source != '' ) ) {
				\Yii::error( "SMS not sent: No loan source detected. Person_id: " . $loan->person_id );
				continue;
			}

			if ( $this->send( $loan, 10 ) ) {
				continue;
			} else {
				continue;
			}

		}

		return 'ok';
	}

	public function actionSendtest() {
		ini_set( 'max_execution_time', 0 );

		$loans = Loan::findAll( [ 'status' => array_values( [ 0 => '11' ] ) ] );

		foreach ( $loans as $loan ) {
			if ( ! isset( $loan->source ) && ( $loan->source != '' ) ) {
				\Yii::error( "SMS not sent: No loan source detected. Person_id: " . $loan->person_id );
				continue;
			}

			if ( $this->send( $loan, 10 ) ) {
				continue;
			} else {
				continue;
			}

		}

		return 'ok';
	}

	public function actionSendbs() {
		ini_set( 'max_execution_time', 0 );

		$loans = Loan::findAll( [ 'status' => array_values( [ 0 => '8' ] ) ] );

		foreach ( $loans as $loan ) {
			if ( ! isset( $loan->source ) && ( $loan->source != '' ) ) {
				\Yii::error( "SMS not sent: No loan source detected. Person_id: " . $loan->person_id );
				continue;
			}

			if ( $this->send( $loan, 11 ) ) {
				continue;
			} else {
				continue;
			}

		}

		return 'ok';
	}

	/**
	 * Send email
	 *
	 * @param Loan $model
	 * @param integer $typeCredico
	 *
	 * @return boolean
	 */
	public static function send( $model, $type, $footer = true, $changes = true ) {
		try {
			return self::_send( $model, $type, $footer, $changes );
		} catch ( \Exception $e ) {
			Yii::error( 'Error while sending BULK SMS.', __METHOD__ );

			return false;
		}
	}

	private function sendBulk( $model, $type, $footer = true, $changes = true ) {
		$phone = $model->person->phone;
		$phone = ltrim( trim( $phone ), "+" );
		$from  = self::_getSource( $model->source );

		if ( $footer ) {
			$msg = self::_getText( $model, $type ) . self::_getTextFooter( $model->source );
		} else {
			$msg = self::_getText( $model, $type );
		}
		$url = "https://traffic.sales.lv/API:0.14/";

		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		curl_setopt( $ch, CURLOPT_POSTFIELDS, [
			"APIKey"  => "0a32e96b4c21e5eac53329701af168b1b8ce4eac",
			"Command" => "SendOne",
			"Number"  => $phone,
			"Sender"  => $from,
			"Content" => $msg,
			"Unicode" => 0
		] );

		$content = curl_exec( $ch );

		$json = json_decode( $content );

		if ( property_exists( $json, "Error" ) ) {
			if ( IS_WEB ) {
				\Yii::$app->getSession()->setFlash( "danger", \Yii::t( "app/mail", "SMS not sent: " . $json->Error ) );
				Yii::error( 'ERROR SENDING SMS' . json_encode( $json ) );
			}

			return false;
		} else {
			if ( IS_WEB ) {
				\Yii::$app->getSession()->setFlash( "success", \Yii::t( "app/mail", "SMS sent successfully." ) );
			}

			if ( $changes ) {
				$typeAction = $type + 30;
				if ( in_array( $typeAction, $model->actions ) ) {
					Changes::setChanges( Loan::TYPE, $model->id, "actions_{$typeAction}", null, $typeAction );
				} else {
					$model->setAttribute( "actions", $model->actions + [ $typeAction => 1 ] );
					$model->save();
				}
			}

			return true;
		}
	}

	private function _send( $model, $type, $footer = true, $changes = true ) {
		$phone = $model->person->phone;
		$phone = ltrim( trim( $phone ), "+" );
		$from  = self::_getSource( $model->source );

		if ( $footer ) {
			$msg = self::_getText( $model, $type ) . self::_getTextFooter( $model->source );
		} else {
			$msg = self::_getText( $model, $type );
		}
		$url = "https://traffic.sales.lv/API:0.14/";

		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		curl_setopt( $ch, CURLOPT_POSTFIELDS, [
			"APIKey"  => "0a32e96b4c21e5eac53329701af168b1b8ce4eac",
			"Command" => "SendOne",
			"Number"  => $phone,
			"Sender"  => $from,
			"Content" => $msg,
			"Unicode" => 0
		] );

		$content = curl_exec( $ch );

		$json = json_decode( $content );

		if ( property_exists( $json, "Error" ) ) {
			if ( IS_WEB ) {
				\Yii::$app->getSession()->setFlash( "danger", \Yii::t( "app/mail", "SMS not sent: " . $json->Error ) );
				Yii::error( 'ERROR SENDING SMS' . json_encode( $json ) );
			}

			return false;
		} else {
			if ( IS_WEB ) {
				\Yii::$app->getSession()->setFlash( "success", \Yii::t( "app/mail", "SMS sent successfully." ) );
			}

			if ( $changes ) {
				$typeAction = $type + 30;
				if ( in_array( $typeAction, $model->actions ) ) {
					Changes::setChanges( Loan::TYPE, $model->id, "actions_{$typeAction}", null, $typeAction );
				} else {
					$model->setAttribute( "actions", $model->actions + [ $typeAction => 1 ] );
					$model->save();
				}
			}

			return true;
		}
	}

	private function _getSource( $type ) {
		switch ( $type ) {
			case "source1":
				return "source1";
				break;
			case "source2":
				return "source2";
			default:
				return "Finlat";
		}
	}

	/**
	 * Get text
	 * @todo dont use 10!!!!!!!!!!!!!!!!
	 *
	 * @param unknown $model
	 * @param unknown $type
	 *
	 * @return string
	 */
	private function _getText( $model, $type ) {
		switch ( $type ) {
			case 1: // Reject loan sms
				return self::_getTextTemplate( self::getPersonName( 'Diemžēl šobrīd nevarēsim  piedāvāt Jums aizdevumu.' ) );
				break;
			case 2: // Information sent to email
				return self::_getTextTemplate( self::getPersonName( 'Nevarējām ar Jums sazināties. Uz Jūsu e-pastu tika nosūtīta veidlapa.' ) );
				break;
			case 3; // Send address
				return self::_getTextTemplate( self::getPersonName( 'Adrese: Maskavas 418 (autoplacī)' ) );
				break;
			case 4: // Send custom sms
				return \Yii::$app->getRequest()->post( "sms_custom_text" );
				break;
			case 5:
				return "Labdien " . self::getPersonName( $model->person->name ) . ",\r\n";
				break;
			case 6:
				return "Labdien " . self::getPersonName( $model->person->name ) . ",\r\n";
				break;
			case 7: // Contact request sms
				return self::_getTextTemplate( self::getPersonName( "Labdien, lai izskatītu Jūsu pietiekumu nepieciešama papildus informācija.\r\n "
				                                                    . "Sazinieties ar mums - 29127491" ) );
				break;
			case 8:
				return "Labdien " . self::getPersonName( $model->person->name ) . ",\r\n";
				break;
			case 9: // Accept, contact us sms
				return self::_getTextTemplate( self::getPersonName( 'Autolīzings Jums ir apstiprināts.' ) );
				break;
			case 10: // Action sendextra
				return self::_getTextTemplate( self::getPersonName( 'Jūsu  pieteikums ir provizoriski akceptēts, nepieciesama papildus informacija' ) );
				break;
			case 11: // Action sendbs
				return self::_getTextTemplate( self::getPersonName( 'Lai akceptētu Jūsu līzinga pieteikumu nepieciešams Jūsu bankas konta pārskats info@carsoutlet.lv' ) );
				break;

			default:
				return "Informacija";
		}
	}

	private function _getTextTemplate( $text ) {
		return self::getPersonName( "Labdien.\r\n\r\n"
		                            . $text . "\r\n\r\n"
		                            . "Ar cieņu,\r\n"
		                            . "Finlat.lv" );
	}

	private function _getTextFooter( $type ) {
		switch ( $type ) {
			case "source1":
				return "\r\n";
				break;
			case "source2":
				return "\r\n";
				break;
			default:
				return "";
		}
	}

	private function _getErrorMsg( $type ) {
		switch ( $type ) {
			case "-400":
				return "Wrong API KEY for the request";
				break;
			case "-500":
				return "Missing required parameters";
				break;
			case "-501":
				return "Wrong “type”, must be “txt” or “bin”";
				break;
			case "-503":
				return "Destination address is blocked";
				break;
			case "-504":
				return "Not available for this operator";
				break;
			case "-508":
				return "Wrong destination address";
				break;
			case "-509":
				return "Wrong message encoding";
				break;
			case "-511":
				return "Number does not exist or operator/owner has been changed";
				break;
			case "-513":
				return "Wrong message length";
				break;
			case "-514":
				return "Sender name is not available for you";
				break;
			case "-515":
				return "Not enough funds to send the message";
				break;
			case "-555":
				return "General system error";
				break;
			default:
				return "Other error type";
		}
	}

	public static function getPersonName( $name ) {
		$replace = [
			'&lt;'   => '',
			'&gt;'   => '',
			'&#039;' => '',
			'&amp;'  => '',
			'&quot;' => '',
			'À'      => 'A',
			'Á'      => 'A',
			'Â'      => 'A',
			'Ã'      => 'A',
			'Ä'      => 'Ae',
			'&Auml;' => 'A',
			'Å'      => 'A',
			'Ā'      => 'A',
			'Ą'      => 'A',
			'Ă'      => 'A',
			'Æ'      => 'Ae',
			'Ç'      => 'C',
			'Ć'      => 'C',
			'Č'      => 'C',
			'Ĉ'      => 'C',
			'Ċ'      => 'C',
			'Ď'      => 'D',
			'Đ'      => 'D',
			'Ð'      => 'D',
			'È'      => 'E',
			'É'      => 'E',
			'Ê'      => 'E',
			'Ë'      => 'E',
			'Ē'      => 'E',
			'Ę'      => 'E',
			'Ě'      => 'E',
			'Ĕ'      => 'E',
			'Ė'      => 'E',
			'Ĝ'      => 'G',
			'Ğ'      => 'G',
			'Ġ'      => 'G',
			'Ģ'      => 'G',
			'Ĥ'      => 'H',
			'Ħ'      => 'H',
			'Ì'      => 'I',
			'Í'      => 'I',
			'Î'      => 'I',
			'Ï'      => 'I',
			'Ī'      => 'I',
			'Ĩ'      => 'I',
			'Ĭ'      => 'I',
			'Į'      => 'I',
			'İ'      => 'I',
			'Ĳ'      => 'IJ',
			'Ĵ'      => 'J',
			'Ķ'      => 'K',
			'Ł'      => 'K',
			'Ľ'      => 'K',
			'Ĺ'      => 'K',
			'Ļ'      => 'K',
			'Ŀ'      => 'K',
			'Ñ'      => 'N',
			'Ń'      => 'N',
			'Ň'      => 'N',
			'Ņ'      => 'N',
			'Ŋ'      => 'N',
			'Ò'      => 'O',
			'Ó'      => 'O',
			'Ô'      => 'O',
			'Õ'      => 'O',
			'Ö'      => 'Oe',
			'&Ouml;' => 'Oe',
			'Ø'      => 'O',
			'Ō'      => 'O',
			'Ő'      => 'O',
			'Ŏ'      => 'O',
			'Œ'      => 'OE',
			'Ŕ'      => 'R',
			'Ř'      => 'R',
			'Ŗ'      => 'R',
			'Ś'      => 'S',
			'Š'      => 'S',
			'Ş'      => 'S',
			'Ŝ'      => 'S',
			'Ș'      => 'S',
			'Ť'      => 'T',
			'Ţ'      => 'T',
			'Ŧ'      => 'T',
			'Ț'      => 'T',
			'Ù'      => 'U',
			'Ú'      => 'U',
			'Û'      => 'U',
			'Ü'      => 'Ue',
			'Ū'      => 'U',
			'&Uuml;' => 'Ue',
			'Ů'      => 'U',
			'Ű'      => 'U',
			'Ŭ'      => 'U',
			'Ũ'      => 'U',
			'Ų'      => 'U',
			'Ŵ'      => 'W',
			'Ý'      => 'Y',
			'Ŷ'      => 'Y',
			'Ÿ'      => 'Y',
			'Ź'      => 'Z',
			'Ž'      => 'Z',
			'Ż'      => 'Z',
			'Þ'      => 'T',
			'à'      => 'a',
			'á'      => 'a',
			'â'      => 'a',
			'ã'      => 'a',
			'ä'      => 'ae',
			'&auml;' => 'ae',
			'å'      => 'a',
			'ā'      => 'a',
			'ą'      => 'a',
			'ă'      => 'a',
			'æ'      => 'ae',
			'ç'      => 'c',
			'ć'      => 'c',
			'č'      => 'c',
			'ĉ'      => 'c',
			'ċ'      => 'c',
			'ď'      => 'd',
			'đ'      => 'd',
			'ð'      => 'd',
			'è'      => 'e',
			'é'      => 'e',
			'ê'      => 'e',
			'ë'      => 'e',
			'ē'      => 'e',
			'ę'      => 'e',
			'ě'      => 'e',
			'ĕ'      => 'e',
			'ė'      => 'e',
			'ƒ'      => 'f',
			'ĝ'      => 'g',
			'ğ'      => 'g',
			'ġ'      => 'g',
			'ģ'      => 'g',
			'ĥ'      => 'h',
			'ħ'      => 'h',
			'ì'      => 'i',
			'í'      => 'i',
			'î'      => 'i',
			'ï'      => 'i',
			'ī'      => 'i',
			'ĩ'      => 'i',
			'ĭ'      => 'i',
			'į'      => 'i',
			'ı'      => 'i',
			'ĳ'      => 'ij',
			'ĵ'      => 'j',
			'ķ'      => 'k',
			'ĸ'      => 'k',
			'ł'      => 'l',
			'ľ'      => 'l',
			'ĺ'      => 'l',
			'ļ'      => 'l',
			'ŀ'      => 'l',
			'ñ'      => 'n',
			'ń'      => 'n',
			'ň'      => 'n',
			'ņ'      => 'n',
			'ŉ'      => 'n',
			'ŋ'      => 'n',
			'ò'      => 'o',
			'ó'      => 'o',
			'ô'      => 'o',
			'õ'      => 'o',
			'ö'      => 'oe',
			'&ouml;' => 'oe',
			'ø'      => 'o',
			'ō'      => 'o',
			'ő'      => 'o',
			'ŏ'      => 'o',
			'œ'      => 'oe',
			'ŕ'      => 'r',
			'ř'      => 'r',
			'ŗ'      => 'r',
			'š'      => 's',
			'ù'      => 'u',
			'ú'      => 'u',
			'û'      => 'u',
			'ü'      => 'ue',
			'ū'      => 'u',
			'&uuml;' => 'ue',
			'ů'      => 'u',
			'ű'      => 'u',
			'ŭ'      => 'u',
			'ũ'      => 'u',
			'ų'      => 'u',
			'ŵ'      => 'w',
			'ý'      => 'y',
			'ÿ'      => 'y',
			'ŷ'      => 'y',
			'ž'      => 'z',
			'ż'      => 'z',
			'ź'      => 'z',
			'þ'      => 't',
			'ß'      => 'ss',
			'ſ'      => 'ss',
			'ый'     => 'iy',
			'А'      => 'A',
			'Б'      => 'B',
			'В'      => 'V',
			'Г'      => 'G',
			'Д'      => 'D',
			'Е'      => 'E',
			'Ё'      => 'YO',
			'Ж'      => 'ZH',
			'З'      => 'Z',
			'И'      => 'I',
			'Й'      => 'Y',
			'К'      => 'K',
			'Л'      => 'L',
			'М'      => 'M',
			'Н'      => 'N',
			'О'      => 'O',
			'П'      => 'P',
			'Р'      => 'R',
			'С'      => 'S',
			'Т'      => 'T',
			'У'      => 'U',
			'Ф'      => 'F',
			'Х'      => 'H',
			'Ц'      => 'C',
			'Ч'      => 'CH',
			'Ш'      => 'SH',
			'Щ'      => 'SCH',
			'Ъ'      => '',
			'Ы'      => 'Y',
			'Ь'      => '',
			'Э'      => 'E',
			'Ю'      => 'YU',
			'Я'      => 'YA',
			'а'      => 'a',
			'б'      => 'b',
			'в'      => 'v',
			'г'      => 'g',
			'д'      => 'd',
			'е'      => 'e',
			'ё'      => 'yo',
			'ж'      => 'zh',
			'з'      => 'z',
			'и'      => 'i',
			'й'      => 'y',
			'к'      => 'k',
			'л'      => 'l',
			'м'      => 'm',
			'н'      => 'n',
			'о'      => 'o',
			'п'      => 'p',
			'р'      => 'r',
			'с'      => 's',
			'т'      => 't',
			'у'      => 'u',
			'ф'      => 'f',
			'х'      => 'h',
			'ц'      => 'c',
			'ч'      => 'ch',
			'ш'      => 'sh',
			'щ'      => 'sch',
			'ъ'      => '',
			'ы'      => 'y',
			'ь'      => '',
			'э'      => 'e',
			'ю'      => 'yu',
			'я'      => 'ya'
		];

		return str_replace( array_keys( $replace ), $replace, $name );
	}

	/**
	 * Finds the Loan model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param string $id
	 *
	 * @return Loan the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel( $id ) {
		if ( ( $model = Loan::findOne( $id ) ) !== null ) {
			return $model;
		} else {
			throw new NotFoundHttpException( 'The requested page does not exist.' );
		}
	}
}
