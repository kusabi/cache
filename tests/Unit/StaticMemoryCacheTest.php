<?php

namespace Kusabi\Cache\Tests\Unit;

use Kusabi\Cache\StaticMemoryCache;
use Kusabi\Cache\Tests\CacheTest;
use Psr\SimpleCache\CacheInterface;

class StaticMemoryCacheTest extends CacheTest
{
    /**
     * {@inheritDoc}
     *
     * @see CacheTest::getCache()
     */
    public function getCache(): CacheInterface
    {
        return new StaticMemoryCache();
    }

    public function testCrossOver()
    {
        $a = $this->getCache();
        $b = $this->getCache();
        $a->set('a', 1);
        $b->set('a', 2);
        $this->assertSame(2, $a->get('a'));
        $this->assertSame(2, $b->get('a'));
    }
}
