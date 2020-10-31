<?php

namespace Kusabi\Cache\Tests\Integration;

use Kusabi\Cache\RedisCache;
use Kusabi\Cache\Tests\CacheTest;
use Predis\Client;
use Psr\SimpleCache\CacheInterface;

class RedisCacheTest extends CacheTest
{
    /**
     * {@inheritDoc}
     *
     * @see CacheTest::getCache()
     */
    public function getCache(): CacheInterface
    {
        return new RedisCache(new Client([

        ]));
    }
}
