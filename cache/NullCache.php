<?php
declare(strict_types=1);
namespace TMC\Cache;

use Psr\SimpleCache\CacheInterface;

class NullCache implements CacheInterface
{
     /**
      * Undocumented function
      *
      * @param string $key
      * @param mixed $default
      * @return mixed
      */
    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->has($key)) {
            return $default;
        }
        return null;
    }

    
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        return true;
    }

    
    public function delete(string $key): bool
    {
        return true;
    }

    
    public function clear(): bool
    {
        return true;
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

    
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        return true;
    }

    
    public function deleteMultiple(iterable $keys): bool
    {
        return true;
    }

    
    public function has(string $key): bool
    {
        return false;
    }
}