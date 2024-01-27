<?php
declare(strict_types=1);

namespace XTC\Cache;

use Psr\SimpleCache\CacheInterface;


/**
 * Abstract class for implementing a simple cache.
 *
 * @package XTC\Cache
 */
abstract class SimpleCacheAbstract implements CacheInterface
{
    /**
     * Indicates cache is enabled
     *
     * @var string
     */
    protected bool $enabled = true;


    /**
     * Disable/Enable cache
     *
     * @param bool $disable The disable flag
     * 
     * @return void
     */
    public function disable(bool $disable = true): bool
    {
        return $this->enabled = !$disable;
    }

    /**
     * Generate a cache key based on the provided key.
     *
     * @param string $key The original key.
     * 
     * @return string The generated cache key.
     */
    public function generateKey(string $key)
    {
        return $key;
    }

    /**
     * Get a value from the cache by key.
     *
     * @param string $key     The key to retrieve the value for.
     * @param mixed  $default The default value to return if the key is not found.
     * 
     * @return mixed The cached value or the default value if not found.
     */
    abstract public function get($key, $default = null);

    /**
     * Check if a key exists in the cache.
     *
     * @param string $key The key to check for existence.
     * 
     * @return bool True if the key exists, false otherwise.
     */
    abstract public function has($key): bool;

    /**
     * Set a value in the cache.
     *
     * @param string   $key   The key to set the value for.
     * @param mixed    $value The value to store in the cache.
     * @param int|null $ttl   Time to live (TTL) in seconds. If null, the value should not expire.
     * 
     * @return bool True if the value was successfully set, false otherwise.
     */
    abstract public function set($key, $value, $ttl = null): bool;

    /**
     * Delete a value from the cache by key.
     *
     * @param string $key The key to delete the value for.
     * 
     * @return bool True if the value was successfully deleted, false otherwise.
     */
    abstract public function delete($key): bool;

    /**
     * Clear the entire cache.
     *
     * @return bool True if the cache was successfully cleared, false otherwise.
     */
    abstract public function clear(): bool;

    /**
     * Get multiple values from the cache by multiple keys.
     *
     * @param iterable $keys    An iterable of keys to retrieve values for.
     * @param mixed    $default The default value to return for keys not found in the cache.
     * 
     * @return iterable An iterable of cached values or default values for keys not found.
     */
    public function getMultiple($keys, $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $this->get($key, $default);
        }
    }

    /**
     * Set multiple values in the cache.
     *
     * @param iterable $values An iterable of key-value pairs to store in the cache.
     * @param int|null $ttl    Time to live (TTL) in seconds. If null, the values should not expire.
     * 
     * @return bool True if all values were successfully set, false otherwise.
     */
    public function setMultiple($values, $ttl = null): bool
    {
        $success = true;
        foreach ($values as $key => $value) {
            if (false == $this->set($key, $value, $ttl)) {
                $success = false;
            }
        }
        return $success;
    }

    /**
     * Delete multiple values from the cache by multiple keys.
     *
     * @param iterable $keys An iterable of keys to delete values for.
     * 
     * @return bool True if all values were successfully deleted, false otherwise.
     */
    public function deleteMultiple($keys): bool
    {
        $success = true;
        foreach ($keys as $key) {
            if (false == $this->delete($key)) {
                $success = false;
            }
        }
        return $success;
    }
}