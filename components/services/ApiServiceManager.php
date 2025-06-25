<?php


namespace app\components\services;


use app\components\HTTPRequester;
use app\models\LoanProgerss;
use app\services\ApiHelper;

class ApiServiceManager {

    private $loan;

    public function __construct( $loan ) {
        $this->loan = $loan;
    }

    public function trySendApiRequest() {
        $loan           = $this->loan;
        $tfBankService  = new TFBankService( $loan );
//		$finanzaService = new FinanzaService( $loan );
        $monenzaService = new MonenzaService( $loan );
//        $inbankService = new InbankService( $loan, \Yii::$app->params["inbankapi_test"] );

        $mogoService = new MogoService( $loan, \Yii::$app->params["mogo_test"] );
        $eLizingsService = new ELizingsService( $loan);

        if ( ApiHelper::isBusinessLoan( $loan->source ) ) {
            $this->sendMonifyApiRequest( $loan );
        } else if ( $tfBankService->shouldSendApiRequest() ) {
            try {
                $tfBankService->sendApiRequest();
            } catch ( \Exception $e ) {
                $this->_log('Error in sending tfBankService request: ' . $e->getMessage());
                print_r( 'Error in sending tfBankService request: ' . $e );
            }
        }

//		if ( $finanzaService->shouldSendApiRequest() ) {
//			try {
//				$finanzaService->sendApiRequest();
//			} catch ( \Exception $e ) {
//				print_r( 'Error in sending finanzaService request: ' . $e );
//			}
//		}

        if ( $monenzaService->shouldSendApiRequest() ) {
            print_r( ' SENDING monenza API REQUEST ' );
            $monenzaService->sendApiRequest();
        } else {
            print_r( ' SHOULD NOT SEND monenza API REQUEST ' );
        }

//
//        if ($inbankService->shouldSendApiRequest()) {
//            try {
//                $inbankService->sendApiRequest();
//                print_r( ' SENDING Inbank API REQUEST ' );
//            } catch (\Exception $e) {
////                print_r( 'Error in sending inbankService request: ' . $e );
//                $this->_log(
//                    'error - Error in sending InbankService request [' . $e->getCode() . ']: ' . $e->getMessage()
//                );
//            }
//        } else {
////            print_r( ' SHOULD NOT SEND inbank API REQUEST ' );
//            $this->_log('info - SHOULD NOT SEND Inbank API REQUEST');
//        }

        if ($eLizingsService->shouldSendApiRequest()) {
            try {
                $eLizingsService->sendApiRequest();
            } catch (\Exception $e) {
                $this->_log(
                    'error - Error in sending eLizingsService request [' . $e->getCode() . ']: ' . $e->getMessage()
                );
            }
        } else {
            $this->_log('info - SHOULD NOT SEND eLizings API REQUEST');
        }

        if ($mogoService->shouldSendApiRequest()) {
            try {
                $mogoService->sendApiRequest();
            } catch (\Exception $e) {
                $this->_log(
                    'error - Error in sending MogoService request [' . $e->getCode() . ']: ' . $e->getMessage()
                );
            }
        } else {
            $this->_log('info - SHOULD NOT SEND Mogo API REQUEST');
        }

    }

    function sendMonifyApiRequest( $loan ) {
        $reqData    = [
            "company" => [
                "age"       => $loan->person->working_time,
                "name"      => $loan->company_name,
                "regNumber" => $loan->person->personal_code,
                "turnover"  => $loan->person->annual_turnover
            ],
            "contact" => [
                "appAmount" => $loan->amount,
                "email"     => $loan->person->email,
                "ipAddress" => $loan->ip_ountry,
                "name"      => $loan->person->name,
                "phone"     => $loan->person->phone
            ]
        ];
        $providerId = 1;
        $this->sendRequest( 'https://hooks.zapier.com/hooks/catch/3854717/p7iwel/', $reqData, $loan, $providerId );
    }

    function sendRequest( $url, $params, $loan, $providerId ) {
        $resp = json_decode( HTTPRequester::HTTPPost( $url, $params ), true );

        if ( ! empty( $resp ) ) {
            if ( $resp->success && $resp->id ) {
                $this->saveApiResponse( $loan, $resp->id, $providerId );
            }
        }
    }

    function saveApiResponse( $loan, $apiResponseId, $providerId, $status = 1, $text = '' ) {

        $loanProgress = $this->getLoanProgress( $loan, $providerId );

        if ( ! $loanProgress ) {
            $loanProgress = new LoanProgerss();
        }

        $loanProgress->loan_id         = $loan->id;
        $loanProgress->amount          = strval( $loan->amount );
        $loanProgress->status          = $status;
        $loanProgress->provider_id     = $providerId;
        $loanProgress->api_response_id = $apiResponseId;
        $loanProgress->text            = $text;


        if ( $loanProgress->save() ) {
            return print_r( "Ok" );
        }

        print_r( [ '$loanProgress->validate' => $loanProgress->validate(), 'errors' => $loanProgress->getErrors() ] );


        return print_r( "Not ok" );
    }

    function getLoanProgress( $model, $providerId ) {
        $progressData = $model->progress;
        $loanProgress = null;

        foreach ( $progressData as $progress ) {
            if ( $progress->provider_id === $providerId ) {
                $loanProgress = $progress;
            }
        }

        return $loanProgress;
    }

    protected function _log( $msg ) {
        $fd  = fopen( \Yii::$app->params["api_data_log_path"], "a+" );
        $str = "[" . date( "Y/m/d h:i:s", time() ) . "] " . $msg;
        fwrite( $fd, $str . "\n" );
        fclose( $fd );
    }
}
