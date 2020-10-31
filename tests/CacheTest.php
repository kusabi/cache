<?php

namespace Kusabi\Cache\Tests;

use DateInterval;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

abstract class CacheTest extends TestCase
{
    /**
     * Get the cache implementation
     *
     * @return CacheInterface
     */
    abstract public function getCache(): CacheInterface;

    /**
     * Bad key data provider
     *
     * @return string[][]
     */
    public function provideBadKeys()
    {
        return [
            ['this_sentence_is_exactly_65_characters_long_which_is_one_too_many'], // 65 characters is too many
            ['test!'], // Only allow characters A-Za-z0-9_.
            ['test?'], // Only allow characters A-Za-z0-9_.
            [''], // Must be at least 1 character long
        ];
    }

    public function tearDown()
    {
        $this->getCache()->clear();
    }

    public function testClear()
    {
        $cache = $this->getCache();
        $cache->set('lorem', 1);
        $cache->set('ipsum', 1);
        $this->assertTrue($cache->clear());
        $this->assertEquals(null, $cache->get('lorem'));
        $this->assertEquals(null, $cache->get('ipsum'));
    }

    public function testDelete()
    {
        $cache = $this->getCache();
        $cache->set('lorem', 1);
        $cache->set('ipsum', 2);
        $this->assertTrue($cache->delete('lorem'));
        $this->assertEquals(null, $cache->get('lorem'));
        $this->assertEquals(2, $cache->get('ipsum'));
    }

    /**
     * @dataProvider provideBadKeys
     */
    public function testDeleteBadKey($key)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->getCache()->delete($key);
    }

    public function testDeleteMultiple()
    {
        $cache = $this->getCache();
        $cache->set('lorem', 1);
        $cache->set('ipsum', 2);
        $cache->set('dolor', 3);
        $cache->deleteMultiple(['lorem', 'dolor']);
        $this->assertFalse($cache->has('lorem'));
        $this->assertTrue($cache->has('ipsum'));
        $this->assertFalse($cache->has('dolor'));
    }

    /**
     * @dataProvider provideBadKeys
     */
    public function testDeleteMultipleBadKey($key)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->getCache()->deleteMultiple(['lorem', $key]);
    }

    /**
     * @dataProvider provideBadKeys
     */
    public function testGetBadKey($key)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->getCache()->get($key);
    }

    public function testGetDefaultValue()
    {
        $this->assertEquals('ipsum', $this->getCache()->get('lorem', 'ipsum'));
    }

    public function testGetDefaultValueNull()
    {
        $this->assertEquals(null, $this->getCache()->get('lorem'));
    }

    public function testGetMultiple()
    {
        $cache = $this->getCache();
        $cache->set('lorem', 1);
        $cache->set('ipsum', 2);
        $this->assertEquals([
            'lorem' => 1,
            'ipsum' => 2,
            'dolor' => null
        ], $cache->getMultiple(['lorem', 'ipsum', 'dolor']));
    }

    /**
     * @dataProvider provideBadKeys
     */
    public function testGetMultipleBadKey($key)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->getCache()->getMultiple(['lorem', $key]);
    }

    public function testHas()
    {
        $cache = $this->getCache();
        $this->assertFalse($cache->has('lorem'));
        $cache->set('lorem', 1);
        $this->assertTrue($cache->has('lorem'));
        $cache->delete('lorem');
        $this->assertFalse($cache->has('lorem'));
    }

    /**
     * @dataProvider provideBadKeys
     */
    public function testHasBadKey($key)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->getCache()->has($key);
    }

    public function testSet()
    {
        $cache = $this->getCache();
        $this->assertTrue($cache->set('lorem', 'ipsum'));
        $this->assertEquals('ipsum', $cache->get('lorem'));
    }

    /**
     * @dataProvider provideBadKeys
     */
    public function testSetBadKey($key)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->getCache()->set($key, 1);
    }

    public function testSetMultiple()
    {
        $cache = $this->getCache();
        $cache->setMultiple([
            'lorem' => 1,
            'ipsum' => 2,
        ]);
        $this->assertEquals(1, $cache->get('lorem'));
        $this->assertEquals(2, $cache->get('ipsum'));
    }

    /**
     * @dataProvider provideBadKeys
     */
    public function testSetMultipleBadKey($key)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->getCache()->setMultiple([
            'lorem' => 1,
            $key => 2
        ]);
    }

    public function testTtl()
    {
        $cache = $this->getCache();
        $cache->set('lorem', 1, 1);
        $cache->set('ipsum', 2, 1);
        $cache->set('dolor', 3, new DateInterval('PT1S'));
        $cache->set('lorem', 1); // The original ttl should be removed now
        $this->assertEquals(1, $cache->get('lorem'));
        $this->assertEquals(2, $cache->get('ipsum'));
        $this->assertEquals(3, $cache->get('dolor'));
        sleep(1);
        $this->assertEquals(1, $cache->get('lorem'));
        $this->assertEquals(null, $cache->get('ipsum'));
        $this->assertEquals(null, $cache->get('dolor'));
    }
}
