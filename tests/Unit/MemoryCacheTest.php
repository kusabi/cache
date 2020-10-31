<?php

namespace Kusabi\Cache\Tests\Unit;

use Kusabi\Cache\MemoryCache;
use Kusabi\Cache\Tests\CacheTest;
use Psr\SimpleCache\CacheInterface;

class MemoryCacheTest extends CacheTest
{
    /**
     * {@inheritDoc}
     *
     * @see CacheTest::getCache()
     */
    public function getCache(): CacheInterface
    {
        return new MemoryCache();
    }

    public function testNoCrossOver()
    {
        $a = $this->getCache();
        $b = $this->getCache();
        $a->set('a', 1);
        $b->set('a', 2);
        $this->assertSame(1, $a->get('a'));
        $this->assertSame(2, $b->get('a'));
    }
}
