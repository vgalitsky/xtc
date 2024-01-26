<?php
use XTC\Cache\NullCache;

use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

/**
 * @covers Cl\SimpleCache\NullCache
 */
class NullCacheTest extends TestCase
{
    /** @var CacheInterface */
    private $cache;

    protected function setUp(): void
    {
        $this->cache = new NullCache();
    }

    public function testGet(): void
    {
        $this->assertNull($this->cache->get('non_existing_key'));
        $this->assertSame('default_value', $this->cache->get('non_existing_key', 'default_value'));
    }

    public function testSet(): void
    {
        $this->assertTrue($this->cache->set('key', 'value'));
        // You can add more assertions based on your implementation details
    }

    public function testDelete(): void
    {
        $this->assertTrue($this->cache->delete('non_existing_key'));
        // You can add more assertions based on your implementation details
    }

    public function testClear(): void
    {
        $this->assertTrue($this->cache->clear());
        // You can add more assertions based on your implementation details
    }

    public function testGetMultiple(): void
    {
        $this->assertSame([], $this->cache->getMultiple(['key1', 'key2']));
        $this->assertSame(['key1' => 'default_value', 'key2' => 'default_value'], $this->cache->getMultiple(['key1', 'key2'], 'default_value'));
    }

    public function testSetMultiple(): void
    {
        $this->assertTrue($this->cache->setMultiple(['key1' => 'value1', 'key2' => 'value2']));
        // You can add more assertions based on your implementation details
    }

    public function testDeleteMultiple(): void
    {
        $this->assertTrue($this->cache->deleteMultiple(['key1', 'key2']));
        // You can add more assertions based on your implementation details
    }

    public function testHas(): void
    {
        $this->assertFalse($this->cache->has('not existing_key'));
    }
}