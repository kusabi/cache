<?php

namespace Kusabi\Cache;

use DateInterval;
use DateTime;
use Predis\Client;
use Psr\SimpleCache\CacheInterface;

class RedisCache extends Cache implements CacheInterface
{
    /**
     * The redis client
     *
     * @var Client
     */
    protected $redis;

    /**
     * RedisCache constructor.
     *
     * @param Client $redis
     */
    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritDoc}
     *
     * @see CacheInterface::clear()
     */
    public function clear()
    {
        return $this->redis->flushall();
    }

    /**
     * {@inheritDoc}
     *
     * @see CacheInterface::delete()
     */
    public function delete($key)
    {
        $this->assertValidKey($key);
        $this->redis->del($key);
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @see CacheInterface::deleteMultiple()
     */
    public function deleteMultiple($keys)
    {
        $this->assertValidKeys($keys);
        $this->redis->del($keys);
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @see CacheInterface::get()
     */
    public function get($key, $default = null)
    {
        $this->assertValidKey($key);
        if (!$this->has($key)) {
            return $default;
        }
        return $this->redis->get($key);
    }

    /**
     * {@inheritDoc}
     *
     * @see CacheInterface::getMultiple()
     */
    public function getMultiple($keys, $default = null)
    {
        $this->assertValidKeys($keys);
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }
        return $results;
    }

    /**
     * {@inheritDoc}
     *
     * @see CacheInterface::has()
     */
    public function has($key)
    {
        $this->assertValidKey($key);
        return (bool) $this->redis->exists($key);
    }

    /**
     * {@inheritDoc}
     *
     * @see CacheInterface::set()
     */
    public function set($key, $value, $ttl = null)
    {
        $this->assertValidKey($key);
        if ($ttl === null) {
            $this->redis->set($key, $value);
            return true;
        }

        $now = new DateTime();
        $then = new DateTime();
        $ttl = $ttl instanceof DateInterval ? $ttl : new DateInterval('PT'.(int) $ttl.'S');
        $then->add($ttl);
        $ttl = $then->getTimestamp() - $now->getTimestamp();
        $this->redis->setex($key, $ttl, $value);
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @see CacheInterface::setMultiple()
     */
    public function setMultiple($values, $ttl = null)
    {
        $this->assertValidKeys(array_keys($values));
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }
}
