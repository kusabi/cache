<?php

namespace Kusabi\Cache;

use DateInterval;
use DateTime;
use Psr\SimpleCache\CacheInterface;

class MemoryCache extends Cache implements CacheInterface
{
    /**
     * The cached items
     *
     * @var array
     */
    protected $cache = [];

    /**
     * The cached items' expiry timestamps
     *
     * @var array
     */
    protected $ttl = [];

    /**
     * {@inheritDoc}
     *
     * @see CacheInterface::clear()
     */
    public function clear()
    {
        $this->cache = [];
        $this->ttl = [];
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @see CacheInterface::delete()
     */
    public function delete($key)
    {
        $this->assertValidKey($key);
        unset($this->cache[$key]);
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
        foreach ($keys as $key) {
            $this->delete($key);
        }
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
        if (isset($this->ttl[$key])) {
            if (time() >= $this->ttl[$key]) {
                unset($this->ttl[$key]);
                unset($this->cache[$key]);
            }
        }
        return $this->cache[$key] ?? $default;
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
        return isset($this->cache[$key]);
    }

    /**
     * {@inheritDoc}
     *
     * @see CacheInterface::set()
     */
    public function set($key, $value, $ttl = null)
    {
        $this->assertValidKey($key);
        $this->cache[$key] = $value;
        if ($ttl !== null) {
            $now = new DateTime();
            $ttl = $ttl instanceof DateInterval ? $ttl : new DateInterval('PT'.(int) $ttl.'S');
            $now->add($ttl);
            $this->ttl[$key] = $now->getTimestamp();
        } else {
            unset($this->ttl[$key]);
        }
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
