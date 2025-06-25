<?php

namespace app\controllers;

use app\base\Controller;
use app\components\services\ELizingsService;
use app\models\Loan;
use app\models\LoanProgerss;
use app\traits\RequestResponseTrait;
use aywan\JsonCanonicalization\JsonCanonicalizationFactory;
use Yii;
use yii\base\ExitException;

class WebhookController extends Controller
{
    use RequestResponseTrait;

    public $enableCsrfValidation = false;

    const ELIZINGS_KEY = 'elizings-finlat-123';

    /**
     * @throws ExitException
     */
    public function actionElizings(): string
    {
        $eLizingsService = new ELizingsService(new Loan());

        if (!$this->request->isPost) {
            $eLizingsService->_log('Webhook Error: no POST request');
            return $this->sendJson([], 400);
        }

        $headers = $this->request->getHeaders();

        if (!$this->request->post()) {
            $eLizingsService->_log('Webhook Error: No POST parameters');
            return $this->sendJson(['error' => 'No POST parameters'], 400);
        }

        if (!$headers->has('X-Hmac-Hash')) {
            $eLizingsService->_log(
                'Webhook Error: Signature not found; Request: ' . json_encode($this->request->post())
            );
            return $this->sendJson(['error' => 'Signature not found'], 400);
        }

        $data = JsonCanonicalizationFactory::getInstance()->canonicalize($this->request->post());

        if (hash_hmac('sha256', $data, self::ELIZINGS_KEY) !== $headers->get('X-Hmac-Hash')) {
            $eLizingsService->_log(
                'Webhook Error: Signature incorrect; Hmac-Hash: ' . $headers->get(
                    'X-Hmac-Hash'
                ) . '; Request: ' . json_encode($this->request->post())
            );
            return $this->sendJson(['error' => 'Signature incorrect'], 400);
        }

        if (!$this->request->post('applicationId')) {
            $eLizingsService->_log('Webhook Error: ApplicationId not found; Request: ' . json_encode($this->request->post()));
            return $this->sendJson(['error' => 'ApplicationId not found'], 400);
        }

        if (!$this->request->post('status')) {
            $eLizingsService->_log('Webhook Error: Status not found; Request: ' . json_encode($this->request->post()));
            return $this->sendJson(['error' => 'Status not found'], 400);
        }

        $loanProgress = LoanProgerss::find()
            ->where(['api_response_id' => $this->request->post('applicationId')])
            ->one();

        if ( !$loanProgress ) {
            $eLizingsService->_log('Webhook Error: Loan Progress not found; Request: ' . json_encode($this->request->post()));
            return $this->sendJson(['error' => 'Loan Progress not found'], 400);
        }

        $loanProgress->api_status = $this->request->post('status');
        $loanProgress->text = 'Status: ' . $this->request->post('status');

        if ($this->request->post('comment')) {
            $loanProgress->text .= '; Status Comment: ' . $this->request->post('comment');
        }

        switch ($this->request->post('status')) {
            case 'accepted':
            case 'pending':
            case 'signed':
            case 'new':
                $loanProgress->status = 3;
                break;
            case 'rejected':
                $loanProgress->status = 2;
                break;
            default:
                $loanProgress->status = 1;
                break;
        }

        if ($loanProgress->save()) {
            $eLizingsService->_log(
                'Webhook Update; Request: ' . json_encode($this->request->post())
            );
            return $this->sendJson(['message' => 'Ok']);
        } else {
            Yii::error('Webhook Error: loanProgress not saved');
            return $this->sendJson(['error' => 'DB error'], 400);
        }
    }
}
