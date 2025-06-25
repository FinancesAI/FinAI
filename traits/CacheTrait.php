<?php

namespace app\traits;

use Yii;
use yii\base\InvalidConfigException;
use yii\caching\Cache;

/**
 * @property Cache $cache
 */
trait CacheTrait
{
    /**
     * @var string
     */
    protected string $cacheComponent = 'cache';
    /**
     * @var Cache
     */
    protected Cache $cacheComponentCached;

    /**
     * @return Cache|null|mixed
     * @throws InvalidConfigException
     */
    public function getCache()
    {
        if (!isset($this->cacheComponentCached)) {
            $this->cacheComponentCached = Yii::$app->get($this->cacheComponent);
        }

        return $this->cacheComponentCached;
    }
}
