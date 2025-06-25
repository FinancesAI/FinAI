<?php

namespace app\components\services;

use app\components\HTTPRequester;
use app\components\utils\StringUtils;
use app\models\LoanProgerss;
use app\services\ApiHelper;

/**
 * Class MonenzaService
 * @package app\components\services
 *
 * Monenza Api documentation
 * https://docs.google.com/document/d/1gcjPifStDLODiHPp5O4Oqzu8FK2v2SIY5Y2aa4Rlooc/edit#heading=h.1t3h5sf
 *
 */
class MonenzaService extends ApiService {
    private $env = 'prod';
    private $testUrl = 'https://int.dev.moneza.lv';
    private $prodUrl = 'https://int.moneza.lv';
    private $baseUrl = null;
    private $authTest = ['username' => 'finlat', 'password' => 'GYf4j$96P((x+pQ9'];
    private $authProd = ['username' => 'finlat', 'password' => 'rTz9]+cQg<VNJ8+t'];
    private $authBase64 = 'ZmlubGF0OkdZZjRqJDk2UCgoeCtwUTk=';

    public function __construct($loan) {
        parent::__construct($loan);
        $this->baseUrl = $this->env === 'test' ? $this->testUrl : $this->prodUrl;
    }

    function getAuthString() {
        return $this->env === 'test'
            ? $this->encodeBase64UsernamePassword($this->authTest)
            : $this->encodeBase64UsernamePassword($this->authProd);
    }

    public function shouldSendApiRequest() {
        $source = (int)$this->loan->source;
        if (ApiHelper::isRefinancingLoan($source)
            || ApiHelper::isConsumerLoan($source)
            || ApiHelper::isOnlineLoan($source)
        ) {
            return true;
        }

        return false;
    }

    public function sendAPIRequest() {
        return $this->sendApplicationRequest();
    }

    /**
     * 3.4 Application request
     */
    public function sendApplicationRequest() {
        $url = $this->baseUrl . '/broker/application';

        $data = json_encode($this->collectData());
        $resp = HTTPRequester::HTTPPostEncoded($url, $data, $this->getAuthString());
        $this->handleResponse($resp);

        return [
            'Request' => $data,
            'Response' => $resp
        ];
    }

    public function collectData() {
        $loan = $this->loan;

        $term = $this->getTerm($loan->term);

        return [
            "broker" => "finlat",
            "application" => [
                "term" => [
                    "value" => $term,
                    "unit" => "MONTHS"
                ],
                "amount" => $loan->amount,
                "purpose" => $this->getPurpose()
            ],
            "client" => [
                "personalId" => $loan->person->personal_code,
                "mobilePhone" => StringUtils::cleanPhone($loan->person->phone),
                "email" => $loan->person->email,
                "gender" => "MALE",
                "language" => "lv",
                "workStatus" => 'WORKING',
                "monthlyIncome" => $loan->person->income,
                "monthlyLiabilities" => $loan->person->outcome
            ]
        ];
    }

    public function getTerm($term) {
        if (!isset($term) || $term === null || $term == 0) {
            return 6;
        } else if ($term > 36) {
            return 36;
        } else {
            return $term;
        }
    }

    public function getPurpose() {
        if (ApiHelper::isRefinancingLoan($this->loan->source)) {
            return 'LOAN_CONSOLIDATION';
        } else {
            return 'OTHER';
        }
    }

    public function handleResponse($response) {
        $result = $this->handleApplicationRequestResponse(json_decode($response, true));
        if ($result['loanProgress'] != null) {
            $this->approveSendRequest($result['loanProgress']);
        } else {
            print_r('Success is false');
        }
    }

    public function approveSendRequest($loanProgress) {
        if ($loanProgress && $loanProgress->api_response_id) {
            $url = $this->baseUrl . '/broker/application/' . $loanProgress->api_response_id . '/approve';

            $data = json_encode([
                'broker' => 'finlat',
                'applicationId' => $loanProgress->api_response_id
            ]);

            $resp = HTTPRequester::HTTPPostEncoded($url, $data, $this->authBase64);
            $this->handleApplicationApproveResponse($loanProgress, json_decode($resp, true));

            print_r([
                'type' => 'approveSendRequest',
                'request' => json_decode($data, true),
                'response' => json_decode($resp, true)
            ]);
        }

    }

    public function handleApplicationRequestResponse($response) {
        if (!empty($response['errors']) && count($response['errors'])) {
            $text = 'Errors: ';
            foreach ($response['errors'] as $error) {
                $property = $error['property'];
                $errorCode = $error['errorCode'];
                $text .= ', Property: ' . $property . ' => ErrorCode: ' . $errorCode;
            }

            $loanProgress = $this->saveApiResponse(null, 16, 2, $text);

            return ['loanProgress' => $loanProgress, 'errors' => true];
        } else if (!empty($response['applicationId']) && !empty($response['clientStatus'])) {
            $status = $response['clientStatus'];

            $loginUrl = !empty($response['loginUrl']) ? $loginUrl = $response['loginUrl'] : '';

            $text = 'Status: ' . $status;
            if ($loginUrl) {
                $text .= ', LoginUrl: ' . $loginUrl;
            }
            $loanProgress = $this->saveApiResponse((int)$response['applicationId'], 16, 1, $text);

            return ['loanProgress' => $loanProgress, 'errors' => false];
        }

        return ['loanProgress' => null, 'errors' => true];
    }

    public function handleApplicationApproveResponse(LoanProgerss $loanProgress, $response) {
        if (!empty($response['loginUrl'])) {
            $loanProgress->text = $loanProgress->text .= ', LoginUrl: ' . $response['loginUrl'];
        } else if (!empty($response['errors'])) {
            $loanProgress->status = 2;

            foreach ($response['errors'] as $error) {
                if (!empty($error['errorCode'])) {
                    $loanProgress->text = $loanProgress->text .= ', ErrorCode: ' . $error['errorCode'];
                }
                if (!empty($error['property'])) {
                    $loanProgress->text = $loanProgress->text .= ', Property: ' . $error['property'];
                }
            }


        }

        $loanProgress->save();
    }


}
