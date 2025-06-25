<?php

namespace app\modules\chat\components;

use Yii;
use yii\base\Component;

/**
 *
 */
class ChatMailer extends Component
{
    /**
     * @var string
     */
    public string $viewPath = '@modules/chat/views/mail';
    /**
     * @var string|array
     */
    public $sender;
    /**
     * @var string
     */
    public string $siteName;

    /**
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->siteName = Yii::$app->params["site_name"];

        if (!isset($this->sender)) {
            $this->sender = Yii::$app->params["app_mail_from"];
        }
    }

    /**
     * @param $to
     * @param $subject
     * @param $view
     * @param array $params
     * @return bool
     */
    public function sendMessage($to, $subject, $view, array $params = []): bool
    {
        $mailer = Yii::$app->mailer;
        $mailer->viewPath = $this->viewPath;
        $mailer->view = Yii::$app->view;

        return $mailer->compose($view, $params)
            ->setTo($to)
            ->setFrom([$this->sender => $this->siteName])
            ->setSubject($subject)
            ->send();
    }
}
