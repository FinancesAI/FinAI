<?php

namespace app\components\services;

use app\components\HTTPRequester;
use app\components\services\ApiService;
use app\components\services\SendApiRequestHelper;
use app\components\utils\StringUtils;
use app\jobs\InbankCheckStatusJob;
use app\services\ApiHelper;
use Yii;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\httpclient\Response;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;

class InbankService extends ApiService
{
    /**
     *
     */
    const PROVIDER_ID = 19;

    /**
     * @var bool
     */
    private bool $test = false;

    /**
     * @var string
     */
    private string $testUrl = 'https://demo-api.inbank.lv/partner/v2';

    /**
     * @var string
     */
    private string $prodUrl = 'https://api.inbank.lv/partner/v2';

    /**
     * @var string
     */
    private string $testApiKey = '71dbd93eff9e551056e2f0c290b38afb';

    /**
     * @var string
     */
    private string $prodApiKey = 'fa763529000430e524d37e7d417d91c8';

    /**
     * @var string
     */
    private string $testShopUuid = '9f6717b9-3865-4426-a43d-8cbd8b5fde49';

    /**
     * @var string
     */
    private string $prodShopUuid = 'ccb37e98-c97b-48fb-8617-c0f3d10640df';

    /**
     * @var string
     */
    private string $testProductCode = 'car_loan_finlat_pp_t';

    /**
     * @var string
     */
    private string $prodProductCode = 'car_loan_finlat_pp';

    /**
     * @var Client
     */
    protected Client $client;

    /**
     * @param $loan
     * @param bool $test
     */
    public function __construct($loan, bool $test = false)
    {
        $this->setTest($test);

        $this->client = $this->getClient();

        parent::__construct($loan);
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        if (!isset($this->client)) {
            return new Client([
                'baseUrl' => $this->getBaseUrl(),
                'requestConfig' => [
                    'format' => Client::FORMAT_JSON,
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->getApiKey()
                    ]
                ],
                'responseConfig' => [
                    'format' => Client::FORMAT_JSON
                ],
            ]);
        }

        return $this->client;
    }

    /**
     * @param $test
     * @return void
     */
    public function setTest($test): void
    {
        $this->test = $test;
    }


    /**
     * @return string
     */
    private function getEnvironmentURL(): string
    {
        return $this->test ? $this->testUrl : $this->prodUrl;
    }

    /**
     * @return string
     */
    private function getBaseUrl(): string
    {
        return $this->getEnvironmentURL() . '/shops/' . $this->getShopUuid();
    }

    /**
     * @return string
     */
    private function getApiKey(): string
    {
        return $this->test ? $this->testApiKey : $this->prodApiKey;
    }

    /**
     * @return string
     */
    private function getShopUuid(): string
    {
        return $this->test ? $this->testShopUuid : $this->prodShopUuid;
    }

    /**
     * @return string
     */
    private function getProductCode(): string
    {
        return $this->test ? $this->testProductCode : $this->prodProductCode;
    }

    /**
     * @return bool
     */
    public function shouldSendApiRequest(): bool
    {
        return ApiHelper::isAutoLoan($this->loan->source)
            || ApiHelper::isConsumerLoan($this->loan->source)
            || ApiHelper::isOnlineLoan($this->loan->source)
            || ApiHelper::isRefinancingLoan($this->loan->source);
    }

    /**
     * @throws HttpException
     * @throws BadRequestHttpException
     */
    public function sendAPIRequest() {
        return $this->sendApplicationRequest();
    }

    /**
     * @return Response|null
     * @throws Exception
     */
    public function sendApplicationDetailsRequest() {
        $loanProgress = $this->getLoanProgress( self::PROVIDER_ID );

        if ( ! $loanProgress ) {
            return null;
        }

        if (!empty($loanProgress->api_response_id)) {
            return $this->client->get('applications/' . $loanProgress->api_response_id)->send();
        }

        return null;
    }

    /**
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function sendApplicationRequest()
    {
        $loan = $this->loan;

        $data = [
            'credit_application' => [
                'product_code' => $this->getProductCode(),
                'amount' => $loan->amount,
                'period' => $loan->term,
                'monthly_income' => $loan->person->income,
                'monthly_financial_obligations' => $loan->person->outcome,
                'employment_position' => 'private_sector_employee',
                'dependants_count' => 0
            ],
            'customer' => [
                'identity_code' => $loan->person->personal_code,
                'first_name' => $loan->person->name,
                'last_name' => $loan->person->surname,
                'gender' => 'm'
            ],

            'customer_contact' => [
                'mobile' => StringUtils::formatPhone($loan->person->phone),
                'email' => $loan->person->email
            ],

            'consents' => [
                [
                    'type' => 'DATA_PROCESSING',
                    'value' => true,
                    'text' => 'Customer data processing consent.'
                ]
            ]
        ];

        $httpResponse = HTTPRequester::HTTPPostBearer(
            $this->getBaseUrl() . '/applications',
            json_encode($data, true),
            $this->getApiKey(),
        );

        $this->_log(
            '[Code: ' . $httpResponse['code'] . '] Response: ' . $httpResponse['response'] . '; Error: "' . $httpResponse['error'] . '"; Request: ' . json_encode(
                $data,
                true
            )
        );

        if ($httpResponse['code'] == 400) {
            throw new BadRequestHttpException('Bad Request');
        }

        if (in_array($httpResponse['code'], [201, 422])) {
            $response = json_decode($httpResponse['response'], true);

            $returnData = [
                'Request' => $data,
                'Response' => $response
            ];

            $response['code'] = $httpResponse['code'];
            $this->handleResponse($response);

        } else {
            throw new HttpException($httpResponse['code'], $httpResponse['error']);
        }
    }

    /**
     * @param $response
     * @return void
     */
    public function handleResponse($response)
    {
        if ($response['code'] == 422) {
            $text = 'Error [422], Unprocessable entity: ' . implode(', ', $response['message']);
            $this->saveApiResponse(0, self::PROVIDER_ID, 2, $text);
        } elseif ($response['code'] == 201) {
            $text = 'Status: ' . $response['status'];

            switch ($response['status']) {
                case 'negative':
                case 'manual_negative':
                case 'cancelled':
                    $status = 2;
                    $text .= '; Decision messages: ' . implode(', ', $response['decision_messages']);
                    break;
                case 'failed':
                    $status = 2;
                    $text .= '; Error: ' . implode(', ', $response['decision_messages']);
                    $this->_log(
                        'error - Error in sending InbankService request: ' . implode(
                            ', ',
                            $response['decision_messages']
                        )
                    );
                    break;
                default:
                    $status = 3;
                    $text .= '; Decision messages: ' . implode(', ', $response['decision_messages']);
                    break;
            }

            $this->saveApiResponse($response['uuid'], self::PROVIDER_ID, $status, $text, $response['status']);

            if (in_array($response['status'], ['pending', 'income_proof_required'])) {
                Yii::$app->queue->push(new InbankCheckStatusJob([
                    'loanId' => $this->loan->id,
                ]));
            }
        }
    }

    /**
     * @param $msg
     * @return void
     */
    protected function _log($msg)
    {
        $fd = fopen(\Yii::$app->params["inbankapi_log_path"], "a+");
        $str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
        fwrite($fd, $str . "\n");
        fclose($fd);
    }

}
