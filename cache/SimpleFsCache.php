<?php
declare(strict_types=1);

namespace TMC\Cache;

use Psr\SimpleCache\CacheInterface;

class SimpleFsCache implements CacheInterface
{
    protected string $path = '';
    protected string $ext = '.tmc.cache';

    public function __construct(string $path)
    {
        $this->path = $path;
    }
     
    public function generateKey(string $key)
    {
        return $key;
    }

    public function getFilePath($key)
    {
        return $this->path.DIRECTORY_SEPARATOR.$this->generateKey($key).$this->ext;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->has($key)) {
            return $default;
        }
        return unserialize(file_get_contents($this->getFilePath($key)));
    }

    
    public function set(string $key, mixed $value, int $ttl = null): bool
    {
        return false === file_put_contents($this->getFilePath($key), serialize($value)) ? false : true;
    }

    
    public function delete(string $key): bool
    {
        return unlink($this->getFilePath($key));
    }

    
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

    
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $item = $this->get($key, $default);
            if (!empty($item)) {
                $result[$key] = $item;
            }
        }
        return $result;
    }

    
    public function setMultiple(iterable $values, int $ttl = null): bool
    {
        $success = true;
        foreach ($values as $key => $value) {
            if (false == $this->set($key, $value, $ttl)) {
                $success = false;
            }
        }
        return $success;
    }

    
    public function deleteMultiple(iterable $keys): bool
    {
        $success = true;
        $result = [];
        foreach ($keys as $key) {
            if (false == $this->delete($key)) {
                $success = false;
            }
        }
        return $success;
    }

    
    public function has(string $key): bool
    {
        $fileName = $this->getFilePath($key);
        return file_exists($fileName) && is_readable($fileName);
    }
}