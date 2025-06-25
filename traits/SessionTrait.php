<?php

namespace app\traits;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\Session;

/**
 * @package app\traits
 * @property Session $session
 */
trait SessionTrait
{
    /**
     * @var string
     */
    protected string $sessionComponent = 'session';
    /**
     * @var Session
     */
    protected Session $sessionComponentCached;

    /**
     * @return Session|null|mixed
     * @throws InvalidConfigException
     */
    public function getSession()
    {
        if (!isset($this->sessionComponentCached)) {
            $this->sessionComponentCached = Yii::$app->get($this->sessionComponent);
        }

        return $this->sessionComponentCached;
    }
}
