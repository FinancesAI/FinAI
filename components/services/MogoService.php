<?php

namespace app\components\services;

use app\components\utils\StringUtils;
use app\jobs\MogoCheckStatusJob;
use app\models\Provider;
use Yii;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\httpclient\Response;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;

class MogoService extends ApiService
{
    /**
     *
     */
    const PROVIDER_ID = 23;

    /**
     * @var bool
     */
    private bool $test = false;

    /**
     * @var string
     */
    private string $testUrl = 'https://demo-lv-rubie-lv.mogodemo.eu/external/api/v1';

    /**
     * @var string
     */
    private string $prodUrl = 'https://lv.rubie.eu/external/api/v1';

    /**
     * @var string
     */
    private string $apiKey = 'zRifQRDJAej6Z68g';

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
                        'Api-Key' => $this->getApiKey()
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
        return $this->getEnvironmentURL();
    }

    /**
     * @return string
     */
    private function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @return bool
     */
    public function shouldSendApiRequest(): bool
    {
        $provider = Provider::findOne(self::PROVIDER_ID);

        if ($provider) {
            $sourcesIdArray = ArrayHelper::getColumn($provider->sources,'id');
            return $sourcesIdArray && in_array($this->loan->source, $sourcesIdArray);
        }

        return false;
    }

    /**
     * @throws HttpException
     * @throws BadRequestHttpException
     */
    public function sendAPIRequest()
    {
        return $this->sendApplicationRequest();
    }

    /**
     * @return Response|null
     * @throws Exception
     */
    public function sendApplicationDetailsRequest(): ?Response
    {
        $loanProgress = $this->getLoanProgress(self::PROVIDER_ID);

        if (!$loanProgress) {
            return null;
        }

        if (!empty($loanProgress->api_response_id)) {
            return $this->client->get('applications/' . $loanProgress->api_response_id)->send();
        }

        return null;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function sendApplicationRequest()
    {
        $loan = $this->loan;

        $data = [
            'service' => [
                'serviceType' => 'near_prime_leaseback',
                'amount' => [
                    'amount' => $loan->amount
                ],
                'term' => [
                    'value' => $loan->term
                ],
            ],
            'client' => [
                'name' => $loan->person->name,
                'surname' => $loan->person->surname,
                'email' => $loan->person->email,
                'phone' => StringUtils::formatPhone($loan->person->phone),
                'clientIdentificator' => $loan->person->personal_code,
                'clientType' => 'p',
                'language' => 'lv',
                'address' => $loan->person->address,
                'monthlyIncome' => [
                    'amount' => $loan->person->income
                ],
            ],
            'promotionCode' => 'ecomerce'
        ];

        $response = $this->getClient()->post('applications', $data)->send();

        $this->_log(
            '[Code: ' . $response->statusCode . '] Response: ' . $response->content . '; Request: ' . json_encode(
                $data,
                true
            )
        );

        $this->handleResponse($response);
    }

    /**
     * @param $applicationId
     * @return array|null
     * @throws Exception
     */
    public function getApplicationStatus($applicationId): ?array
    {
        $response = $this->getClient()->get('applications/' . $applicationId)->send();

        if ($response->statusCode == 200 && isset($response->data['status'])) {
            return [
                'status' => $response->data['status'],
                'reason' => $response->data['status_reason_motivation']['status_reason']['reason'] ?? null
            ];
        }

        return null;
    }

    /**
     * @param $response
     * @return void
     * @throws Exception
     */
    public function handleResponse($response)
    {
        if ($response->statusCode == 201) {

            if (isset($response->data['data']['applicationId'])) {
                $applicationStatus = $this->getApplicationStatus($response->data['data']['applicationId']);
            } else {
                $this->_log('error - No Application ID in response');
                return;
            }

            if (!isset($applicationStatus['status'])) {
                $this->_log('error - No Application Status');
                return;
            }

            $text = 'Status: ' . $applicationStatus['status'];

            if (isset($applicationStatus['reason'])) {
                $text .= ' ; Status Reason: ' . $applicationStatus['reason'];
            }

            switch ($applicationStatus) {
                case 'cant_offer':
                case 'duplicates':
                    $status = 2;
                    break;
                default:
                    $status = 3;
                    break;
            }

            $this->saveApiResponse($response->data['data']['applicationId'], self::PROVIDER_ID, $status, $text, $applicationStatus);

            if ($status == 3) {
                Yii::$app->queue->push(
                    new MogoCheckStatusJob([
                        'loanId' => $this->loan->id,
                    ])
                );
            }
        } else {
            $text = 'Error [' . $response->statusCode . '], ';

            if (isset($response->data['error'])) {
                $text .= '' . $response->data['error'] . ': ';
            }

            if (isset($response->data['input']) && is_array($response->data['input'])) {
                $text .= implode(', ', array_keys($response->data['input']));
            }

            $this->saveApiResponse(0, self::PROVIDER_ID, 2, $text);
        }
    }

    /**
     * @param $msg
     * @return void
     */
    protected function _log($msg)
    {
        $fd = fopen(\Yii::$app->params["mogo_log_path"], "a+");
        $str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
        fwrite($fd, $str . "\n");
        fclose($fd);
    }

}
