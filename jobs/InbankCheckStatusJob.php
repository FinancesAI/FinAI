<?php

namespace app\jobs;

use app\components\services\InbankService;
use app\models\Loan;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Class InbankCheckStatusJob.
 */
class InbankCheckStatusJob extends BaseObject implements JobInterface
{
    /**
     * @var
     */
    public $loanId;

    /**
     * @var string[]
     */
    protected array $pendingStatuses = [
        'pending',
        'income_proof_required'
    ];

    /**
     * @var array|string[]
     */
    protected array $approvedStatuses = [
        'positive'
    ];

    /**
     * @inheritdoc
     * @throws \yii\httpclient\Exception
     */
    public function execute($queue)
    {
        $loan = Loan::findOne($this->loanId);

        if ($loan == null) {
            Yii::error('[InbankCheckStatusJob error] incorrect loanId: "' . $this->loanId . '"');
            return false;
        }

        $inbankService = new InbankService($loan, Yii::$app->params["inbankapi_test"]);

        $response = $inbankService->sendApplicationDetailsRequest();

        if ($response == null) {
            Yii::error('[InbankCheckStatusJob error] missing response for loanId: ' . $this->loanId);
            return false;
        }

        $loanProgress = $inbankService->getLoanProgress(InbankService::PROVIDER_ID);

        switch ($response->statusCode) {
            case 200:
                $data = $response->getData();

                $apiStatus = $data['credit_application']['decision_status'] ?? null;

                if ($apiStatus == null) {
                    Yii::error('[InbankCheckStatusJob error] missing apiStatus for loanId: ' . $this->loanId);
                    return false;
                }

                switch (true) {
                    case $this->isPendingStatus($apiStatus):
                        $queue->delay(2 * 60)->push($this);
                        return false;
                    case $this->isApprovedStatus($apiStatus):
                        $loanProgress->status = 3;
                        break;
                    default:
                        $loanProgress->status = 2;
                        break;
                }

                $loanProgress->api_status = $apiStatus;
                $loanProgress->text = 'Status: ' . $apiStatus . '; Decision messages: ' .
                    implode(
                        ', ',
                        $data['credit_application']['decision_messages']
                    );

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
            Yii::error('[InbankCheckStatusJob error] loan # ' . $this->loanId . ' not saved');
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
}
