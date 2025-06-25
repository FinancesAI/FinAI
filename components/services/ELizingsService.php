<?php

namespace app\components\services;

use app\components\utils\StringUtils;
use app\models\Loan;
use app\services\ApiHelper;
use yii\httpclient\Client;
use yii\httpclient\Exception;

class ELizingsService extends ApiService
{
    /**
     *
     */
    const PROVIDER_ID = 25;

    /**
     * @var bool
     */
    private bool $test = false;

    /**
     * @var string
     */
    private string $testUrl = '';

    /**
     * @var string
     */
    private string $prodUrl = 'https://epartneri.lv/partner/form';

    /**
     * @var string
     */
    private string $testApiKey = '';

    /**
     * @var string
     */
    private string $prodApiKey = '16_1k7mzmifmgi8nwo1lfygdtrsubsrrtgw';


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
                        'Authorization' => 'Bearer ' . $this->getApiKey(),
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
    private function getBaseUrl(): string
    {
        return $this->test ? $this->testUrl : $this->prodUrl;
    }

    /**
     * @return string
     */
    private function getApiKey(): string
    {
        return $this->test ? $this->testApiKey : $this->prodApiKey;
    }

    /**
     * @return bool
     */
    public function shouldSendApiRequest(): bool
    {
        $source = (int)$this->loan->source;

        return ApiHelper::isAutoLoan($source)
            || ApiHelper::isConsumerLoan($source)
            || ApiHelper::isOnlineLoan($source)
            || ApiHelper::isRefinancingLoan($source);
    }

    /**
     * @return null
     * @throws Exception
     */
    public function sendAPIRequest()
    {
        return $this->sendApplicationRequest();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function sendApplicationRequest()
    {
        $loan = $this->loan;

        $data = [
            'application' => [
                'amount' => $loan->amount,
                'term' => $loan->term,
                'productId' => $this->getProductId($loan),
            ],
            'client' => [
                'name' => $loan->person->name,
                'surname' => $loan->person->surname,
                'email' => $loan->person->email,
                'phone' => StringUtils::formatPhone($loan->person->phone),
                'personCode' => $loan->person->personal_code,
                'workPlace' => $loan->person->workplace,
                'workTime' => $loan->person->length_of_service,
                'workSalary' => $loan->person->salary,
            ],
        ];

        // Сервер API epartneri.lv выдает ошибки при charset=UTF-8 в Content-Type
        // и при передачи данных а клиент непосредственно в виде масива, т.е.
        // нужно использовать setContent(), а не setData() и передавать RAW данные

        $response = $this->getClient()
            // Фикс - преобразование json вручную
            ->post('receive', json_encode($data))
            // Фикс API - удаление "charset=UTF-8" так как ругается на Content-Type: application/json; charset=UTF-8
            ->addHeaders(['content-type' => 'application/json'])
            ->send();

        $this->_log(
            '[Code: ' . $response->statusCode . '] Response: ' . $response->content . '; Request: ' . json_encode(
                $data
            )
        );
        $this->handleResponse($response);
    }

    /**
     * @param Loan $loan
     * @return int
     */
    protected function getProductId(Loan $loan): int
    {
        switch (true) {
            case ApiHelper::isAutoLoan($loan->source):
                return 5;
            case ApiHelper::isRefinancingLoan($loan->source):
                return 3;
            default:
                return 2;
        }
    }

    /**
     * @param $response
     * @return void
     */
    public function handleResponse($response)
    {
        if ($response->isOk) {
            $applicationId = $response->data['applicationId'];

            if (isset($response->data['error'])) {
                $text = 'Error: "' . $response->data['error'] . '"';
                $status = 2;
            } elseif (isset($applicationId)) {
                $text = 'applicationId: "' . $applicationId . '"';
                $status = 3;
            } else {
                $text = 'Unknown Error';
                $status = 2;
            }

            $this->saveApiResponse($applicationId ?: 0, self::PROVIDER_ID, $status, $text);
        } else {
            $text = 'HTTP Error [' . $response->statusCode . ']';

            if (isset($response->data['error'])) {
                $text .= ', "' . $response->data['error'] . '"';
            }

            $this->saveApiResponse(0, self::PROVIDER_ID, 2, $text);
        }
    }

    /**
     * @param $msg
     * @return void
     */
    public function _log($msg)
    {
        $fd = fopen(\Yii::$app->params["elizings_log_path"], "a+");
        $str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
        fwrite($fd, $str . "\n");
        fclose($fd);
    }

}
