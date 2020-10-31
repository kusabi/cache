<?php

namespace Kusabi\Cache;

use Psr\SimpleCache\CacheInterface;

abstract class Cache implements CacheInterface
{
    /**
     * Is this a valid key
     *
     * @param string $key
     *
     * @return bool
     */
    protected function validKey(string $key)
    {
        $length = strlen($key);
        return $length >= 1 && $length <= 64 && preg_match('/[^A-Za-z0-9_.]/', $key) == false;
    }

    /**
     * Throw an exception if the key is not valid
     *
     * @param string $key
     *
     * @return void
     */
    protected function assertValidKey(string $key)
    {
        if ($this->validKey($key) === false) {
            throw new InvalidKeyException($key);
        }
    }

    /**
     * Throw an exception if any of the keys are not valid
     *
     * @param array|iterable $keys
     *
     * @return void
     */
    protected function assertValidKeys($keys)
    {
        foreach ($keys as $key) {
            if ($this->validKey($key) === false) {
                throw new InvalidKeyException($key);
            }
        }
    }
}
