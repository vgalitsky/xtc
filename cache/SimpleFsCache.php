<?php
declare(strict_types=1);

namespace XTC\Cache;


class SimpleFsCache extends SimpleCacheAbstract
{
    /**
     * The path to cache storage
     *
     * @var string
     */
    protected string $path = '';

    /**
     * The cache file extensionb
     *
     * @var string
     */
    protected string $ext = '.tmc.cache';

    /**
     * The constructor
     *
     * @param string $path The path for cache files storage
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }
     
    /**
     * {@inheritDoc}
     */
    public function generateKey(string $key)
    {
        return $key;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilePath($key)
    {
        return $this->path.DIRECTORY_SEPARATOR.$this->generateKey($key).$this->ext;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $key): bool
    {
        if (!$this->enabled) {
            return false;
        }

        $fileName = $this->getFilePath($key);
        return file_exists($fileName) && is_readable($fileName);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }
        return unserialize(file_get_contents($this->getFilePath($key)));
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $key, $value, int $ttl = null): bool
    {
        if (!$this->enabled) {
            return false;
        }
        return false === file_put_contents($this->getFilePath($key), serialize($value)) ? false : true;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $key): bool
    {
        return unlink($this->getFilePath($key));
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): bool
    {
        $fullCache = glob($this->path.DIRECTORY_SEPARATOR."*{$this->ext}", GLOB_ERR);
        if (false === $fullCache) {
            return false;
        }
        $success = true;
        foreach ($fullCache as $cacheFile) {
            if (false === unlink($cacheFile)) {
                $success = false;
            }
        }
        return $success;
    }
}