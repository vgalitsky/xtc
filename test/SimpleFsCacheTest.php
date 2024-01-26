<?php

declare(strict_types=1);

use XTC\Cache\SimpleFsCache;
use PHPUnit\Framework\TestCase;

/**
 * @covers TMC\Cache\SimpleFsCache
 */
class SimpleFsCacheTest extends TestCase
{
    private const CACHE_PATH = '/tmp/cache'; // Update this path accordingly

    protected function setUp(): void
    {
        // Create a directory for testing
        mkdir(self::CACHE_PATH);
    }

    protected function tearDown(): void
    {
        // Remove the directory after testing
        //$this->removeDirectory(self::CACHE_PATH);
    }

    private function removeDirectory(string $path): void
    {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
        }
        rmdir($path);
    }

    public function testGenerateKey(): void
    {
        $cache = new SimpleFsCache(self::CACHE_PATH);

        $key = 'test_key';

        $this->assertEquals($key, $cache->generateKey($key));
    }

    public function testGetNonExistentKey(): void
    {
        $cache = new SimpleFsCache(self::CACHE_PATH);

        $key = 'non_existent_key';

        $this->assertNull($cache->get($key));
    }

    public function testSetAndGet(): void
    {
        $cache = new SimpleFsCache(self::CACHE_PATH);

        $key = 'test_key';
        $value = 'test_value';

        $this->assertTrue($cache->set($key, $value));
        $this->assertEquals($value, $cache->get($key));
    }

    public function testSetWithTTL(): void
    {
        $cache = new SimpleFsCache(self::CACHE_PATH);

        $key = 'test_key';
        $value = 'test_value';
        $ttl = 60;

        $this->assertTrue($cache->set($key, $value, $ttl));
        $this->assertEquals($value, $cache->get($key));
    }

    public function testDelete(): void
    {
        $cache = new SimpleFsCache(self::CACHE_PATH);

        $key = 'key_to_delete';
        $value = 'value_to_delete';

        $cache->set($key, $value);

        $this->assertTrue($cache->delete($key));
        $this->assertNull($cache->get($key));
    }

    public function testClearCache(): void
    {
        $cache = new SimpleFsCache(self::CACHE_PATH);

        $key = 'key_to_clear';
        $value = 'value_to_clear';

        $cache->set($key, $value);

        $this->assertTrue($cache->clear());
        $this->assertNull($cache->get($key));
    }

    public function testGetMultiple(): void
    {
        $cache = new SimpleFsCache(self::CACHE_PATH);

        $keys = ['key1', 'key2', 'key3'];
        $values = ['value1', 'value2', 'value3'];

        foreach ($keys as $index => $key) {
            $cache->set($key, $values[$index]);
        }

        $expectedResult = ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'];

        $this->assertEquals($expectedResult, $cache->getMultiple($keys));
    }

    public function testSetMultiple(): void
    {
        $cache = new SimpleFsCache(self::CACHE_PATH);

        $data = ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'];

        $this->assertTrue($cache->setMultiple($data));
    }

    public function testDeleteMultiple(): void
    {
        $cache = new SimpleFsCache(self::CACHE_PATH);

        $keys = ['key1', 'key2', 'key3'];

        foreach ($keys as $key) {
            $cache->set($key, 'value');
        }

        $this->assertTrue($cache->deleteMultiple($keys));

        foreach ($keys as $key) {
            $this->assertNull($cache->get($key));
        }
    }

    public function testHas(): void
    {
        $cache = new SimpleFsCache(self::CACHE_PATH);

        $key = 'test_key';
        $value = 'test_value';

        $cache->set($key, $value);

        $this->assertTrue($cache->has($key));
        $this->assertFalse($cache->has('non_existent_key'));
    }
}
