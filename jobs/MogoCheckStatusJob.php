<?php

namespace app\jobs;

use app\components\services\MogoService;
use app\models\Loan;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Class MogoCheckStatusJob.
 */
class MogoCheckStatusJob extends BaseObject implements JobInterface
{
    /**
     * @var
     */
    public $loanId;

    /**
     * @var string[]
     */
    protected array $pendingStatuses = [
    ];

    /**
     * @var array|string[]
     */
    protected array $approvedStatuses = [
        'issued',
        'rent_issued',
    ];

    /**
     * @var array|string[]
     */
    protected array $cantOfferStatuses = [
        'cant_offer',
        'client_not_reachable',
        'duplicates',
        'refusing'
    ];

    /**
     * @inheritdoc
     * @throws \yii\httpclient\Exception
     */
    public function execute($queue)
    {
        $loan = Loan::findOne($this->loanId);

        if ($loan == null) {
            Yii::error('[MogoCheckStatusJob error] incorrect loanId: "' . $this->loanId . '"');
            return false;
        }

        $mogoService = new MogoService($loan, Yii::$app->params["mogo_test"]);

        $response = $mogoService->sendApplicationDetailsRequest();

        if ($response == null) {
            Yii::error('[MogoCheckStatusJob error] missing response for loanId: ' . $this->loanId);
            return false;
        }

        $loanProgress = $mogoService->getLoanProgress(MogoService::PROVIDER_ID);

        switch ($response->statusCode) {
            case 200:
                $data = $response->getData();

                $apiStatus = $data['status'] ?? null;
                $apiStatusReason = $data['status_reason_motivation']['status_reason']['reason'] ?? '';

                if ($apiStatus == null) {
                    Yii::error('[MogoCheckStatusJob error] missing apiStatus for loanId: ' . $this->loanId);
                    return false;
                }

                switch (true) {
                    case $this->isCantOfferStatus($apiStatus):
                        $loanProgress->status = 2;
                        break;
                    case $this->isApprovedStatus($apiStatus):
                        $loanProgress->status = 3;
                        break;
                    default:
                        $queue->delay(5 * 60)->push($this);
                        return false;
                }

                $loanProgress->api_status = $apiStatus;
                $loanProgress->text = 'Status: ' . $apiStatus . '; Status Reason: ' . $apiStatusReason;

                break;
            case 404:
                $loanProgress->status = 2;
                $loanProgress->text = 'Error [404], Application not found';
                break;
            default:
                $loanProgress->status = 2;
                $loanProgress->text = 'Error [' . $response->statusCode . ']';
                break;
        }

        if ($loanProgress->save()) {
            return true;
        } else {
            Yii::error('[MogoCheckStatusJob error] loan # ' . $this->loanId . ' not saved');
            return false;
        }
    }

    /**
     * @param $status
     * @return bool
     */
    protected function isPendingStatus($status): bool
    {
        return in_array($status, $this->pendingStatuses);
    }

    /**
     * @param $status
     * @return bool
     */
    protected function isApprovedStatus($status): bool
    {
        return in_array($status, $this->approvedStatuses);
    }

    /**
     * @param $status
     * @return bool
     */
    protected function isCantOfferStatus($status): bool
    {
        return in_array($status, $this->cantOfferStatuses);
    }
}
