<?php


namespace app\components\services;


use app\models\Loan;
use app\models\LoanProgerss;

abstract class ApiService {

	/**
	 * @var Loan
	 */
	protected $loan;

	public function __construct( $loan ) {
		$this->loan = $loan;
	}

	public abstract function shouldSendApiRequest();

	public abstract function sendAPIRequest();

	public abstract function handleResponse( $response );

	/**
	 * @param string $apiResponseId - what id do we receive from Response
	 * @param int $providerId - Partner unique id saved in user table
	 * @param int $status - 1 (In progress) or 2 (Rejected) or 3 (Accepted)
	 * @param string $text - Note to save in the Loan progress
	 *
	 * @return mixed
	 */
	function saveApiResponse( $apiResponseId, $providerId, $status = 1, $text = '', $apiStatus = '' ) {
		$loanProgress = $this->getLoanProgress( $providerId );

		if ( ! $loanProgress ) {
			$loanProgress = new LoanProgerss();
		}

		$loanProgress->loan_id         = $this->loan->id;
		$loanProgress->amount          = strval( $this->loan->amount );
		$loanProgress->status          = (int) $status;
		$loanProgress->provider_id     = $providerId;
		$loanProgress->api_response_id = (string) $apiResponseId;
		$loanProgress->text            = $text;
        $loanProgress->api_status            = (string) $apiStatus;

		if ( $loanProgress->save() ) {
			return $loanProgress;
		}

//		print_r( [ '$loanProgress->validate' => $loanProgress->validate(), 'errors' => $loanProgress->getErrors() ] );

		return $loanProgress;
	}

	function getLoanProgress( $providerId ) {
		$progressData = $this->loan->progress;
		$loanProgress = null;

		foreach ( $progressData as $progress ) {
			if ( $progress->provider_id === $providerId ) {
				$loanProgress = $progress;
			}
		}

		return $loanProgress;
	}

	function encodeBase64UsernamePassword(array $auth) {
        return base64_encode($auth['username'] . ':' . $auth['password']);
    }

    protected function _log( $msg ) {
        $fd  = fopen( \Yii::$app->params["api_data_log_path"], "a+" );
        $str = "[" . date( "Y/m/d h:i:s", time() ) . "] " . $msg;
        fwrite( $fd, $str . "\n" );
        fclose( $fd );
    }
}
