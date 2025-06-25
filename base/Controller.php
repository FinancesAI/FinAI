<?php

namespace app\base;

use app\traits\CurrentUserTrait;
use app\traits\RequestResponseTrait;
use app\traits\SessionTrait;
use Yii;
use yii\base\ExitException;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\View;

/**
 * @package app\base
 * @property View $view
 */
class Controller extends \yii\web\Controller
{
    use RequestResponseTrait, CurrentUserTrait, SessionTrait;

    /**
     * @param $data
     * @param int $statusCode
     * @return bool
     * @throws ExitException
     */
    public function sendJson($data, int $statusCode = 200): bool
    {
        $this->response->format = Response::FORMAT_JSON;
        $this->response->data = $data;
        $this->response->statusCode = $statusCode;
        $this->response->send();

        Yii::$app->end();

        return true;
    }

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (!Yii::$app->user->isGuest) {
            $this->initUserData();
            $this->updateOnline();
        }

        return parent::beforeAction($action);
    }

    /**
     * @return void
     */
    protected function initUserData()
    {
        $currentUser = $this->getCurrentUser();

        $this->view->params['countersMessagesNew'] = Yii::$app->getModule('chat')->chatMessageManager->getNewMessagesCount(
            $currentUser->id
        );
    }

    /**
     * @return void
     */
    protected function updateOnline()
    {
        $user = $this->getCurrentUser();

        $lastOnline = $this->session->get('lastOnline');

        if ($user && ($lastOnline == null || time() - $lastOnline > Yii::$app->params['onlineThreshold'])) {
            $lastOnline = time();
            $this->session->set('lastOnline', $lastOnline);
            $user->updateOnline($lastOnline);
        }
    }
}
