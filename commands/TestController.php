<?php

namespace app\commands;


use app\components\HTTPRequester;
use app\components\services\ApiServiceManager;
use app\components\services\FinanzaService;
use app\components\services\InbankService;
use app\components\services\MogoService;
use app\components\services\MonenzaService;
use app\components\services\TFBankService;
use app\models\Loan;
use app\models\User;

class TestController extends \yii\console\Controller {

    public function actionMogo5() {

        $mogoService = new MogoService( new Loan(), false);

        try {
            $applicationStatus = $mogoService->getApplicationStatus('1314439');
            print_r ($applicationStatus) . "\n\n";
        } catch (\Exception $exception) {
            echo($exception->getCode()) . "\n";
            echo($exception->getMessage()) . "\n";
        }

    }

    public function actionMogo3() {

        $mogoService = new MogoService( new Loan(), false);

        try {
            $response = $mogoService->getClient()->get('applications/1314439')->send();

            echo $response->isOk . "\n\n";
            echo $response->statusCode . "\n\n";
            print_r($response->getData()) . "\n";
        } catch (\Exception $exception) {
            echo($exception->getCode()) . "\n";
            echo($exception->getMessage()) . "\n";
        }

    }

    public function actionMogo4() {

        $mogoService = new MogoService( new Loan(), false);

        $data = [
            'service' => [
                'serviceType' => 'near_prime_leaseback',
                'amount' => [
                    'amount' => '5.45'
                ],
                'term' => [
                    'value' => '10'
                ],
            ],
            'client' => [
                'name' => "John",
                'surname' => "Doe",
                'email' => "test@mail.com",
                'phone' => "+334 323 23 23",
                'clientIdentificator' => "8735626355",
                'clientType' => 'p',
                'language' => 'lv',
                'address' => "Test address",
                'monthlyIncome' => [
                    'amount' => "1055"
                ],
            ],
        ];


        try {
            $response = $mogoService->getClient()->post('applications', $data)->send();

            echo $response->isOk . "\n\n";
            echo $response->statusCode . "\n\n";
            echo $response->content . "\n\n";
            print_r($response->getData()) . "\n";
        } catch (\Exception $exception) {
            echo($exception->getCode()) . "\n";
            echo($exception->getMessage()) . "\n";
        }

    }

    public function actionMogo2()
    {
        $ch = curl_init();

        $params = '{
  "service": {
    "serviceType": "installment",
    "commissionType": "linear",
    "invoiceDay": 1,
    "firstPayment": {
      "amount": "5.45"
    },
    "amount": {
      "amount": "5.45"
    },
    "term": {
      "value": 10
    }
  },
  "client": {
    "name": "John",
    "surname": "Doe",
    "email": "test@mail.com",
    "phone": "+334 323 23 23",
    "phone2": "+334 323 23 23",
    "clientIdentificator": "8735626355",
    "clientType": "p",
    "language": "en",
    "gender": "male",
    "address": "Test address",
    "ipAddress": "127.0.0.1",
    "monthlyIncome": {
      "amount": 1055
    },
    "monthlyAdditionalIncome": {
      "amount": 1055
    },
    "monthlyExpenses": {
      "amount": 1055
    },
    "passportNumber": "8778355233",
    "IDCardNumber": "4478355233"
  },
  "warrantor": {
    "name": "John",
    "surname": "Doe",
    "phone": "+334 323 23 23",
    "email": "test@mail.com",
    "clientIdentificator": "82786232323",
    "address": "Test address",
    "monthlyIncome": {
      "amount": 1055
    },
    "monthlyExpenses": {
      "amount": 1055
    }
  },
  "vehicle": {
    "carData": "url",
    "vinNo": "WAUZZZ4E54N010598",
    "plateNo": "HP9772",
    "mileage": "210000",
    "vehicleMark": "AUDI",
    "vehicleModel": "A4",
    "bodyType": "hatchback",
    "fuelType": "petrol",
    "year": 2018,
    "transmissionType": "automatic",
    "engineCapacity": "2.0",
    "url": "http://www.url.com",
    "driveSide": "left",
    "price": "15000",
    "carWeight": "1200",
    "technicalCertificateNo": "AE871331"
  },
  "checkbox": {
    "moneyInCash": true
  },
  "consentsAttributes": {
    "marketing_consent": {
      "agrees": "1",
      "content": "test"
    },
    "privacy_policy_consent": {
      "agrees": "1",
      "content": "test"
    },
    "beneficiary_consent": {
      "agrees": "1",
      "content": "test"
    },
    "credit_history_consent": {
      "agrees": "1",
      "content": "test"
    },
    "vsaa_consent": {
      "agrees": "1",
      "content": "test"
    },
    "legacy_credit_consent": {
      "agrees": "1",
      "content": "test"
    },
    "pep_consent": {
      "agrees": "1",
      "content": "test"
    },
    "third_person_data_consent": {
      "agrees": "1",
      "content": "test"
    },
    "rent_consent": {
      "agrees": "1",
      "content": "test"
    },
    "spouse_credit_history_consent": {
      "agrees": "1",
      "content": "test"
    },
    "spouse_privacy_policy_consent": {
      "agrees": "1",
      "content": "test"
    }
  },
  "gaClientId": "string",
  "requestNumber": "string",
  "verificationMethod": "string",
  "credyAffiliateData": "string",
  "doAffiliateData": "string",
  "affiseData": "string",
  "googleAdsData": "string",
  "payoutType": "transfer",
  "promotionCode": "string",
  "companyId": "string",
  "baseCompany": "mogo",
  "postbackCircleTmtData": "string",
  "bdsClientId": "string",
  "bdsClientUserAgent": "string",
  "digestJson": "string",
  "short_lead_id": 0
}';

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Api-Key: zRifQRDJAej6Z68g'
        ]);

        curl_setopt($ch, CURLOPT_URL, 'https://demo-lv-rubie-lv.mogodemo.eu/api/v1/applications');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        $response = curl_exec($ch);

        echo curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n\n";

        if (curl_error($ch)) {
            echo 'error:' . curl_error($ch) . "\n\n";
        }

        curl_close($ch);

        echo $response . "\n\n";
    }

    public function actionMogo() {

        $mogoService = new MogoService( new Loan(), true);

        $response = $mogoService->getClient()->createRequest()->setMethod('POST')->setContent(
            '{
  "service": {
    "serviceType": "installment",
    "commissionType": "linear",
    "invoiceDay": 1,
    "firstPayment": {
      "amount": "5.45"
    },
    "amount": {
      "amount": "5.45"
    },
    "term": {
      "value": 10
    }
  },
  "client": {
    "name": "John",
    "surname": "Doe",
    "email": "test@mail.com",
    "phone": "+334 323 23 23",
    "phone2": "+334 323 23 23",
    "clientIdentificator": "8735626355",
    "clientType": "p",
    "language": "en",
    "gender": "male",
    "address": "Test address",
    "ipAddress": "127.0.0.1",
    "monthlyIncome": {
      "amount": 1055
    },
    "monthlyAdditionalIncome": {
      "amount": 1055
    },
    "monthlyExpenses": {
      "amount": 1055
    },
    "passportNumber": "8778355233",
    "IDCardNumber": "4478355233"
  },
  "warrantor": {
    "name": "John",
    "surname": "Doe",
    "phone": "+334 323 23 23",
    "email": "test@mail.com",
    "clientIdentificator": "82786232323",
    "address": "Test address",
    "monthlyIncome": {
      "amount": 1055
    },
    "monthlyExpenses": {
      "amount": 1055
    }
  },
  "vehicle": {
    "carData": "url",
    "vinNo": "WAUZZZ4E54N010598",
    "plateNo": "HP9772",
    "mileage": "210000",
    "vehicleMark": "AUDI",
    "vehicleModel": "A4",
    "bodyType": "hatchback",
    "fuelType": "petrol",
    "year": 2018,
    "transmissionType": "automatic",
    "engineCapacity": "2.0",
    "url": "http://www.url.com",
    "driveSide": "left",
    "price": "15000",
    "carWeight": "1200",
    "technicalCertificateNo": "AE871331"
  },
  "checkbox": {
    "moneyInCash": true
  },
  "consentsAttributes": {
    "marketing_consent": {
      "agrees": "1",
      "content": "test"
    },
    "privacy_policy_consent": {
      "agrees": "1",
      "content": "test"
    },
    "beneficiary_consent": {
      "agrees": "1",
      "content": "test"
    },
    "credit_history_consent": {
      "agrees": "1",
      "content": "test"
    },
    "vsaa_consent": {
      "agrees": "1",
      "content": "test"
    },
    "legacy_credit_consent": {
      "agrees": "1",
      "content": "test"
    },
    "pep_consent": {
      "agrees": "1",
      "content": "test"
    },
    "third_person_data_consent": {
      "agrees": "1",
      "content": "test"
    },
    "rent_consent": {
      "agrees": "1",
      "content": "test"
    },
    "spouse_credit_history_consent": {
      "agrees": "1",
      "content": "test"
    },
    "spouse_privacy_policy_consent": {
      "agrees": "1",
      "content": "test"
    }
  },
  "gaClientId": "string",
  "requestNumber": "string",
  "verificationMethod": "string",
  "credyAffiliateData": "string",
  "doAffiliateData": "string",
  "affiseData": "string",
  "googleAdsData": "string",
  "payoutType": "transfer",
  "promotionCode": "string",
  "companyId": "string",
  "baseCompany": "mogo",
  "postbackCircleTmtData": "string",
  "bdsClientId": "string",
  "bdsClientUserAgent": "string",
  "digestJson": "string",
  "short_lead_id": 0
}'
        )->send();

        echo $response->isOk . "\n\n";
        echo $response->statusCode . "\n\n";
        print_r($response) . "\n";

    }

    public function actionInbank() {

        $inbankService = new InbankService( new Loan());

        $response = $inbankService->getClient()->get('applications/e8f072d4-9eee-4d4a-821c-8b121944bb2b')->send();

        echo $response->isOk . "\n\n";
        echo $response->statusCode . "\n\n";
        print_r($response->getData()) . "\n";

    }

    public function actionTfbank($ApplicationId) {

        $TFBankService = new TFBankService( new Loan());

        $url = 'https://atlasapi2.tfbank.se/public_api/api/v2/Drafts/GetDraft';

        $params = [
            'ApplicationId' => $ApplicationId
        ];

        $resp = HTTPRequester::HTTPPost( $url, $params, [ 'username' => 'LTeam', 'password' => 'ROUQ4TrytAoj74G' ]);
//        $resp = json_decode( $resp, true );

//        $url = 'https://atlasapi2.tfbank.se/public_api/odata/RepositoryDrafts';
//
//        $params = [
//            '$filter' => 'ApplicationInfo/PublicId eq 51584544'
//        ];
//
//        $query = http_build_query( $params );

//        $url = 'https://atlasapi2.tfbank.se/public_api/odata/RepositoryDrafts?$filter=ApplicationInfo/PublicId%20eq%201060069';
//
//        $ch    = curl_init( $url);
//        curl_setopt( $ch, CURLOPT_USERPWD, 'LTeam' . ":" . 'ROUQ4TrytAoj74G' );
//        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
//        curl_setopt( $ch, CURLOPT_HEADER, false );
//        $response = curl_exec( $ch );
//        curl_close( $ch );
//
////        $resp = HTTPRequester::HTTPGet( $url, $params, [ 'username' => 'LTeam', 'password' => 'ROUQ4TrytAoj74G' ]);


        print_r($resp) . "\n";

    }


    public function actionIndex() {

        $loan = Loan::findOne(5843);

        $apiServiceManager = new ApiServiceManager($loan);
        $apiServiceManager->trySendApiRequest();

//		$monenzaService = new MonenzaService( $loan );
//
//		print_r( $monenzaService->sendAPIRequest() );

    }


    public function actionReal() {
        $loan = Loan::findOne(5840);

        if ($this->personHaveRealEstate($loan)) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function actionGen() {
        $password = 'sX_=!&zTr3\BTe(~';

        $hash = crypt($password, \Yii::$app->params["salt"]);


        echo $hash;
    }

    function personHaveRealEstate($loan) {
        // Vai Jums pieder nekustamais īpašums?
        return $loan['realEstate'];
    }


    public function actionFinanza() {
        $loan = Loan::findOne(5840);

        $fService = new FinanzaService($loan);

        if ($fService->shouldSendApiRequest()) {
            $fService->sendAPIRequest();
        }


    }

    public function actionMoneza() {
        $loan = Loan::findOne(5840);

        $mService = new MonenzaService($loan);

        if ($mService->shouldSendApiRequest()) {
            print_r($mService->sendAPIRequest());
        }


    }

    public function sendApprove($loan) {
        $monenzaService = new MonenzaService($loan);
        if ($monenzaService->shouldSendApiRequest()) {
//			$resp = $monenzaService->approveSendRequest();

//			return print_r( $resp, true );
        } else {
            return print_r('shouldSendApiRequest returns FALSE', true);
        }
    }


    public function trySendApiRequest($loan) {
        $monenzaService = new MonenzaService($loan);

        if ($monenzaService->shouldSendApiRequest()) {
            print_r('should send api request is TRUE');

            $resp = $monenzaService->sendApiRequest();
            print_r($resp);
        } else {
            print_r('should send api request is FALSE');
        }
    }


}
