<?php

namespace app\components\services;

use app\components\HTTPRequester;
use app\services\ApiHelper;

class TFBankService extends ApiService {

    private $username;
    private $password;
    private $auth;
    const PROVIDER_ID = 6;

    public function __construct( $loan ) {
        parent::__construct( $loan );
        $this->username = 'LTeam';
        $this->password = 'ROUQ4TrytAoj74G';
        $this->auth     = [ 'username' => $this->username, 'password' => $this->password ];
    }

    public function shouldSendApiRequest() {
        $source = (int) $this->loan->source;
        return ApiHelper::isConsumerLoan( $source )
            || ApiHelper::isRefinancingLoan( $source )
            || ApiHelper::isOnlineLoan( $source )
            || ApiHelper::isAutoLoan( $source );
    }

    public function sendAPIRequest() {
        $loan = $this->loan;

        $reqData = [
            "Customer"        => [
                "Addresses"          => [
                    [
                        "City"   => "Rīga",
                        "Street" => $loan->person->address,
                        "Region" => [
                            "Country" => "LVA"
                        ]
                    ]
                ],
                "Contacts"           => [
                    "CellPhone" => '+371' . $loan->person->phone,
                    "Email"     => $loan->person->email
                ],
                "Identification"     => [],
                "Employment"         => [
                    [
                        "IncomeVerified" => $loan->person->income * 12,
                        "Income"         => $loan->person->income * 12,
                        "EmployerName"   => $loan->person->workplace,
                        "OccupationType" => "Unknown"
                    ]
                ],
                "PersonalInfo"       => [
                    "FirstName"     => $loan->person->name,
                    "LastName"      => $loan->person->surname,
                    "Gender"        => "",
                    "MaritalStatus" => "",
                    "Ssn"           => $loan->person->personal_code, // test person-code: "040882-10342"
                ],
                "CountryOfResidence" => "LVA",
                "KnowYourCustomer"   => [
                    "PoliticallyExposedPerson" => false,
                    "BeneficialOwner"          => true,
                    "Citizenship"              => []
                ],
            ],
            "Purpose"         => [
                [
                    "MetaData" => [
                        [
                            "Key"   => "LenderName",
                            "Value" => "Aizdevēja nosaukums SIA",
                            "Type"  => "string"
                        ]
                    ],
                    "Cost"     => $loan->amount,
                ]
            ],
            "ApplicationInfo" => [
                "RepaymentPeriod" => 48,
                "Product"         => "LvaCashLoan",
                "PurposeOfLoan"   => "Consumption"
            ],
            "ExternalPartner" => "LTeam",
            "BankInfo"        => [
                "Iban" => "LV66BANK0000000000000"
            ]
        ];


        try {
            $this->sendAuthRequest(
            // stage url: 'https://atlasapi2.tfbank.se/public_api/api/V2/Application/Register'
            // Test url: 'https://atlasapitest.tfbank.se/public_api/api/V2/Application/Register',
                'https://atlasapi2.tfbank.se/public_api/api/V2/Application/Register',
                $reqData,
                $this->auth );
        } catch ( \Exception $e ) {
            $this->_log("An error has occurred: "  . $e->getMessage());
            $this->_log("Request: "  . json_encode($reqData));
            print_r( 'an error has occurred: ' . $e );
        }
    }

    function sendAuthRequest( $url, $params, $auth ) {
        $response = HTTPRequester::HTTPPost( $url, $params, $auth);
        $resp = json_decode( $response, true );

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->_log("JSON ERROR MSG:  "  . json_last_error_msg());
            $this->_log("JSON ERROR RESPONSE:  "  . $response);
        }

        $res  = [
            "Request"  => $params,
            "Response" => $resp
        ];

        $this->_log("Result -  "  . json_encode($res));

        print_r( $res );

        if ( ! empty( $resp['Decision'] ) ) {
            $this->handleResponse( $resp );
        } else {
            $this->_log("Errors from TF bank response:  "  . json_encode($resp));
            print_r( [ 'Errors from TF bank response' => $resp ] );
        }
    }

    public function handleResponse( $response ) {
        switch ( $response['Decision'] ) {
            case 'Approved':
                $status = 3;
                break;
            case 'Investigation':
            case 'Error':
                $status = 1;
                break;
            case  'Rejected':
            case 'Invalid':
                $status = 2;
                break;
            default:
                $status = 2;
                break;
        }

        $this->_log("STATUS: "  . $status . " (" . $status . ")");
        print_r( '---$status---: ' . $status );

        if ( $status > 0 ) {

            $text = $response['Decision'];

            if ( ! empty( $response['RejectionReason'] ) ) {
                $text .= ' - ' . 'Code: ' . $response['RejectionReason']['Code'] . ', RejectionReason: ' . $response['RejectionReason']['Message'];
            }

            $this->saveApiResponse( $response["ApplicationId"], TFBankService::PROVIDER_ID, $status, $text, $response['Decision'] );

            // If Status is approved then we can send also document
            if ( $status == 3 ) {
                $resp = $this->sendDocument( $response["ApplicationId"] );
            }
        }
    }

    public function sendDocument( string $applicationId ) {

        if ( ! $this->loan->document ) {
            return 'No document found';
        }

//		$baseUrl = [
//			'test' => 'https://webservicetest.TFBank.se:50002/api/refin/',
//			'prod' => 'https://www.finlat.lv/document/'
//		];

        $file_url = 'https://www.finlat.lv/document/' . $this->loan->document;
        $file     = file_get_contents( $file_url );
        $base64   = ''//'data:application/pdf;base64,'
            . base64_encode( $file );


        $req = [
            "ApplicationId" => $applicationId,
            "Complete"      => false,
            "Content"       => $base64,// (base64 content)
            "ContentType"   => "application/pdf", // application/pdf
            "DocumentType"  => "BankStatement", // BankStatement
            "FileName"      => $this->loan->document // "string"
        ];


        //print_r( [ 'sendDocument Req' => json_encode( $req ) ] );

        $url = 'https://atlasapi2.tfbank.se/public_api/api/V2/Application/AddDocument';

        $resp = HTTPRequester::HTTPPost( $url, $req, $this->auth );

//		print_r( [ 'Request' => $req, 'Response' => $resp ] );

        return $resp;
    }

    protected function _log($msg) {
        $fd = fopen(\Yii::$app->params["tfbankapi_log_path"], "a+");
        $str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
        fwrite($fd, $str . "\n");
        fclose($fd);
    }


}
