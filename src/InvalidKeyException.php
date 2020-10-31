<?php

namespace Kusabi\Cache;

use Psr\SimpleCache\InvalidArgumentException;

class InvalidKeyException extends \InvalidArgumentException implements InvalidArgumentException
{
    public function __construct($key)
    {
        parent::__construct("Invalid cache key '{$key}'");
    }
}
